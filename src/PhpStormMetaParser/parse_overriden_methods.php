<?php

use Efabrica\PHPStanRules\Type\DynamicMethodReturnType\PhpStormMetaDynamicMethodReturnTypeExtension;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Parser;
use PhpParser\ParserFactory;

$phpStormMetaFiles = include __DIR__ . '/find_phpstorm_meta_files.php';

$parserFactory = new ParserFactory();
$parser = $parserFactory->create(ParserFactory::PREFER_PHP7);

$classesMethodsAndTypes = [];
/** @var SplFileInfo $phpStormMetaFile */
foreach ($phpStormMetaFiles as $phpStormMetaFile) {
    $phpStormMetaContent = file_get_contents($phpStormMetaFile->getRealPath());
    $classesMethodsAndTypes = array_merge_recursive($classesMethodsAndTypes, getClasssesMethodsAndTypes($parser, $phpStormMetaContent));
}

$services = [];
foreach ($classesMethodsAndTypes as $class => $methodsAndTypes) {
    $services[] = [
        'factory' => PhpStormMetaDynamicMethodReturnTypeExtension::class,
        'arguments' => [
            $class,
            $methodsAndTypes,
        ],
        'tags' => [
            'phpstan.broker.dynamicMethodReturnTypeExtension',
        ],
    ];
}

if ($services === []) {
    return [];
}

return [
    'services' => $services,
];

function getClasssesMethodsAndTypes(Parser $parser, string $content): array
{
    $stmts = $parser->parse($content);

    $traverser = new NodeTraverser();

    $overrideVisitor = new class extends NodeVisitorAbstract
    {
        private $uses = [];

        private $classesMethodsAndTypes = [];

        public function enterNode(Node $node)
        {
            if ($node instanceof Use_) {
                foreach ($node->uses as $use) {
                    $this->uses[$use->getAlias()->name] = $use->name->parts;
                }
                return null;
            }

            if (!$node instanceof FuncCall) {
                return null;
            }

            if ((string)$node->name !== 'override') {
                return null;
            }

            if (!isset($node->args[1])) {
                return null;
            }

            $call = $node->args[0]->value;
            if (!$call instanceof StaticCall) {
                return null;
            }

            $classNameParts = $call->class->parts;
            $alias = $classNameParts[0] ?? null;

            if ($alias && isset($this->uses[$alias])) {
                unset($classNameParts[0]);
                $classNameParts = array_merge($this->uses[$alias], $classNameParts);
            }

            $class = implode('\\', $classNameParts);
            $method = $call->name->name;

            $map = $node->args[1]->value;
            if (!$map instanceof FuncCall) {
                return null;
            }

            $mapArgs = $map->args[0]->value;
            if (!$mapArgs instanceof Array_) {
                return null;
            }

            /** @var ArrayItem $mapArg */
            foreach ($mapArgs->items as $mapArg) {
                if ($mapArg->key instanceof String_ && $mapArg->key->value === '' && $mapArg->value instanceof String_) {
                    $this->classesMethodsAndTypes[$class][$method] = $mapArg->value->value;
                }
            }
        }

        public function getClasssesMethodsAndTypes(): array
        {
            return $this->classesMethodsAndTypes;
        }
    };

    $traverser->addVisitor($overrideVisitor);
    $traverser->traverse($stmts);
    return $overrideVisitor->getClasssesMethodsAndTypes();
}

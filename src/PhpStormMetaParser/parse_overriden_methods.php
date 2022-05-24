<?php

declare(strict_types=1);

use Efabrica\PHPStanRules\PhpStormMetaParser\NodeVisitor\OverrideVisitor;
use Efabrica\PHPStanRules\Type\DynamicMethodReturnType\PhpStormMetaDynamicMethodReturnTypeExtension;
use PhpParser\NodeTraverser;
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
    $overrideVisitor = new OverrideVisitor();
    $traverser->addVisitor($overrideVisitor);
    $traverser->traverse($stmts);
    return $overrideVisitor->getClasssesMethodsAndTypes();
}

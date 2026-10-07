<?php

declare(strict_types=1);

use Efabrica\PHPStanRules\PhpStormMetaParser\NodeVisitor\OverrideVisitor;
use Efabrica\PHPStanRules\Type\DynamicMethodReturnType\PhpStormMetaDynamicMethodReturnTypeExtension;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;

if (!function_exists('getClasssesMethodsAndTypes')) {
    /**
     * @return array<string, array<string, string>>
     */
    function getClasssesMethodsAndTypes(Parser $parser, string $content): array
    {
        $stmts = $parser->parse($content) ?: [];
        $traverser = new NodeTraverser();
        $overrideVisitor = new OverrideVisitor();
        $traverser->addVisitor($overrideVisitor);
        $traverser->traverse($stmts);
        return $overrideVisitor->getClasssesMethodsAndTypes();
    }
}

/** @var iterable<SplFileInfo> $phpStormMetaFiles */
$phpStormMetaFiles = include __DIR__ . '/find_phpstorm_meta_files.php';

$parserFactory = new ParserFactory();
$parser = $parserFactory->createForNewestSupportedVersion();

$scanFiles = [];
$classesMethodsAndTypes = [];
foreach ($phpStormMetaFiles as $phpStormMetaFile) {
    $scanFiles[] = $filepath = $phpStormMetaFile->getRealPath();
    $phpStormMetaContent = (string)file_get_contents($phpStormMetaFile->getRealPath());
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
    'parameters' => [
        'scanFiles' => $scanFiles,
    ],
    'services' => $services,
];

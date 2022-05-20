<?php

$phpStormMetaFiles = include __DIR__ . '/find_phpstorm_meta_files.php';


$classesMethodsAndTypes = [];
/** @var SplFileInfo $phpStormMetaFile */
foreach ($phpStormMetaFiles as $phpStormMetaFile) {
    // TODO create more sophisticated instead of regex

    $phpStormMetaContent = file_get_contents($phpStormMetaFile->getRealPath());
    $pattern = '/override\((?P<class>.*?)::(?P<method>.*?)\(\),(.*?)map(.*?)\'\' => \'(?P<type>.*?)\'/s';

    preg_match_all($pattern, $phpStormMetaContent, $matches);

    if (!isset($matches[0][0])) {
        continue;
    }

    $matchesCount = count($matches[0]);
    for ($i = 0; $i < $matchesCount; $i++) {
        $className = $matches['class'][$i];
        if (!class_exists($className)) {
            continue;
        }

        $reflectionClass = new ReflectionClass($className);
        $class = $reflectionClass->getName();

        $method = $matches['method'][$i];
        $type = $matches['type'][$i];

        $classesMethodsAndTypes[$class][$method] = $type;
    }
}

$services = [];
foreach ($classesMethodsAndTypes as $class => $methodsAndTypes) {
    $services[] = [
        'factory' => 'Efabrica\PHPStanRules\Type\DynamicMethodReturnType\PhpStormMetaDynamicMethodReturnTypeExtension',
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

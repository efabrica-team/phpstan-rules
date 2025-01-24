<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Collector\Schema;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use ReflectionClass;

/**
 * @implements Collector<Class_, array{string, bool, string, int}>
 */
final class SchemaDefinitions implements Collector
{
    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope)
    {
        if (!$node instanceof Class_) {
            return null;
        }

        /** @var class-string */
        $className = ($node->namespacedName) ? $node->namespacedName->toString() : '';
        if (!strpos($className, '\\Schema\\')) {
             return null;
        }
        $reflectionClass = new ReflectionClass($className);
        $reflectionConstructor = $reflectionClass->hasMethod('__construct') ? $reflectionClass->getMethod('__construct') : null;
        $reflectionConstructorParameters = ($reflectionConstructor) ? $reflectionConstructor->getParameters() : null;

        $params = [];
        if ($reflectionConstructorParameters) {
            foreach ($reflectionConstructorParameters as $arg) {
                $tmp = [];
                $tmp['name'] = $arg->getName();
                $tmp['type'] = (string) $arg->getType();
                $tmp['key'] = $arg->getPosition();

                $params[] = $tmp;
            }
        }
        if (empty($params)) {
            return null;
        }
        return [$className, $reflectionClass->isAbstract(), (string)json_encode($params), $node->getLine()];
    }
}

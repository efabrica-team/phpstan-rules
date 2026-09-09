<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Collector\Schema;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\VerbosityLevel;
use function is_null;
use function json_encode;
use function strpos;

/**
 * @implements Collector<Class_, array{string, bool, string, int}>
 */
final class SchemaDefinitions implements Collector
{
    private ReflectionProvider $reflectionProvider;

    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): ?array
    {
        $className = !is_null($node->namespacedName) ? $node->namespacedName->toString() : '';
        if (strpos($className, '\\Schema\\') === false) {
             return null;
        }
        if (!$this->reflectionProvider->hasClass($className)) {
            return null;
        }
        $classReflection = $this->reflectionProvider->getClass($className);
        if (!$classReflection->hasConstructor()) {
            return null;
        }

        $params = [];
        $constructorVariant = $classReflection->getConstructor()->getVariants()[0] ?? null;
        if ($constructorVariant !== null) {
            foreach ($constructorVariant->getParameters() as $position => $parameter) {
                $params[] = [
                    'name' => $parameter->getName(),
                    'type' => $parameter->getType()->describe(VerbosityLevel::typeOnly()),
                    'key' => $position,
                ];
            }
        }
        if ($params === []) {
            return null;
        }
        return [$className, $classReflection->isAbstract(), (string)json_encode($params), $node->getLine()];
    }
}

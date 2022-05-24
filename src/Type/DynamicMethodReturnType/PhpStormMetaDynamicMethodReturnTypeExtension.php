<?php

namespace Efabrica\PHPStanRules\Type\DynamicMethodReturnType;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Type;

class PhpStormMetaDynamicMethodReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function __construct(private string $className, private array $methodsReturnTypes, private TypeStringResolver $typeStringResolver)
    {
    }

    public function getClass(): string
    {
        return $this->className;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        $methodName = $methodReflection->getName();
        return in_array($methodName, array_keys($this->methodsReturnTypes), true);
    }

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): ?Type
    {
        $methodName = $methodReflection->getName();
        return $this->typeStringResolver->resolve($this->methodsReturnTypes[$methodName]);
    }
}

<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\General;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ObjectType;

/**
 * @implements Rule<MethodCall>
 */
final class DisableCallMethodInObjectMethodRule implements Rule
{
    private string $sourceObject;

    private string $sourceMethod;

    private string $calledObject;

    private string $calledMethod;

    public function __construct(string $sourceObject, string $sourceMethod, string $calledObject, string $calledMethod)
    {
        $this->sourceObject = $sourceObject;
        $this->sourceMethod = $sourceMethod;
        $this->calledObject = $calledObject;
        $this->calledMethod = $calledMethod;
    }

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @throws ShouldNotHappenException
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$scope->isInClass()) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (!$classReflection instanceof ClassReflection) {
            return [];
        }

        $className = $classReflection->getName();
        $sourceClassType = new ObjectType($className);
        if (!$sourceClassType->isInstanceOf($this->sourceObject)->yes()) {
            return [];
        }

        if ($scope->getFunctionName() !== $this->sourceMethod) {
            return [];
        }

        $callerType = $scope->getType($node->var);
        if (!$callerType instanceof ObjectType || !$callerType->isInstanceOf($this->calledObject)->yes()) {
            return [];
        }

        $methodName = $this->getNameValue($node->name, $scope);
        if ($methodName !== $this->calledMethod) {
            return [];
        }

        $file = $scope->getFile();
        $errors[] = RuleErrorBuilder::message('Method ' . $className . '::' . $scope->getFunctionName() . '() called ' . $callerType->getClassName() . '::' . $methodName . '().')->file($file)->line($node->getLine())->build();
        return $errors;
    }

    /**
     * @param Identifier|Expr $name
     */
    private function getNameValue($name, Scope $scope): ?string
    {
        if ($name instanceof Identifier) {
            return $name->toString();
        }
        $nameType = $scope->getType($name);
        if ($nameType instanceof ConstantStringType) {
            return $nameType->getValue();
        }
        return null;
    }
}

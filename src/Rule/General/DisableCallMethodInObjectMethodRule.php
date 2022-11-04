<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\General;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

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

    public function processNode(Node $node, Scope $scope): array
    {
        $class = (isset($node->var->class)) ? $node->var->class->toString() : null;
        if (!$node instanceof MethodCall ||
            !isset($node->name->name) ||
            $node->name->name !== $this->calledMethod ||
            $scope->getFunctionName() !== $this->sourceMethod ||
            !$scope->isInClass() ||
            (
                $scope->getClassReflection() !== null &&
                !$scope->getClassReflection()->is($this->sourceObject)
            ) ||
            $class === null ||
            !new $class() instanceof $this->calledObject
        ) {
            return [];
        }
        $file = $scope->getFile();
        $errors[] = RuleErrorBuilder::message('Method ' . ($scope->getClassReflection() ? $scope->getClassReflection()->getName() : '') . '::' . $scope->getFunctionName() . '() called ' . $class . '::' . $node->name->name . '().')->file($file)->line($node->getLine())->build();
        return $errors;
    }
}

<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\General;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Tracy\Debugger;

final class DisableCallMethodInObjectMethodRule implements Rule
{
    private string $objectMethod;

    private string $calledMethod;

    public function __construct(string $objectMethod, string $calledMethod)
    {
        $this->objectMethod = $objectMethod;
        $this->calledMethod = $calledMethod;
    }

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (
            !$node instanceof MethodCall ||
            $node->name->name !== $this->calledMethod ||
            $scope->getFunctionName() !== $this->objectMethod ||
            !$scope->isInClass()
        ) {
            return [];
        }
        $file = $scope->getFile();
        $errors[] = RuleErrorBuilder::message('Method ' . $scope->getClassReflection()->getName() . '::' . $scope->getFunctionName() . ' is called with ' . $node->name->name . '() option.')->file($file)->line($node->getLine())->build();
        return $errors;
    }
}

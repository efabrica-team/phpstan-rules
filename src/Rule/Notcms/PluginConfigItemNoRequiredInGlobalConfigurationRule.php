<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\Notcms;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

final class PluginConfigItemNoRequiredInGlobalConfigurationRule implements Rule
{
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (get_class($node) !== MethodCall::class) {
            return [];
        }
        if ($node->name->name !== 'setRequired') {
            return [];
        }
        if ($scope->getFunctionName() !== 'globalConfiguration') {
            return [];
        }
        if (!$scope->isInClass()) {
            return [];
        }
        $file = $scope->getFile();
        $errors[] = RuleErrorBuilder::message('Method ' . $scope->getClassReflection()->getName() . '::' . $scope->getFunctionName() . ' is called with ' . $node->name->name . '() option.')->file($file)->line($node->getLine())->build();
        return $errors;
    }
}

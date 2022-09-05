<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\Notcms;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

final class PluginConfigItemNoRequiredInPageConfigurationRule implements Rule
{
    public function getNodeType(): string
    {
        return ClassMethod::class;
        return MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($scope->getClassReflection()->getName() === 'App\PluginWithSetRequiredMin') {
            \Tracy\Debugger::dump('Name by reflection: ' . $scope->getClassReflection()->getName());
            \Tracy\Debugger::dump('Parent class by reflection:');
            \Tracy\Debugger::dump($scope->getClassReflection()->getParentClass());
            \Tracy\Debugger::dump('Parent class by native reflection:');
            \Tracy\Debugger::dump($scope->getClassReflection()->getNativeReflection()->getParentClass());
            \Tracy\Debugger::dump('Implements interface Efabrica\Cms\Core\Plugin\PluginDefinitionInterface by reflection:');
            \Tracy\Debugger::dump($scope->getClassReflection()->implementsInterface('Efabrica\Cms\Core\Plugin\PluginDefinitionInterface'));
            \Tracy\Debugger::dump('Interfaces by reflection:');
            \Tracy\Debugger::dump($scope->getClassReflection()->getInterfaces());
            \Tracy\Debugger::$maxDepth = 3;
            $file = $scope->getFile();
            $errors[] = RuleErrorBuilder::message('test fail.')->file($file)->line($node->getLine())->build();
            return $errors;
        }
        if (
            !$node instanceof MethodCall ||
            $node->name->name !== 'setRequired' ||
            $scope->getFunctionName() !== 'pageConfiguration' ||
            !$scope->isInClass()
        ) {
            return [];
        }
        $file = $scope->getFile();
        $errors[] = RuleErrorBuilder::message('test fail.')->file($file)->line($node->getLine())->build();
        $errors[] = RuleErrorBuilder::message('Method ' . $scope->getClassReflection()->getName() . '::' . $scope->getFunctionName() . ' is called with ' . $node->name->name . '() option.')->file($file)->line($node->getLine())->build();
        return $errors;
    }
}

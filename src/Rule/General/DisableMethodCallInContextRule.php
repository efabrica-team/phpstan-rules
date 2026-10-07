<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\General;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ObjectType;
use function count;
use function explode;
use function implode;

/**
 * @implements Rule<MethodCall>
 */
final class DisableMethodCallInContextRule implements Rule
{
    /** @var array<array{context: string, disabled:string}> */
    private array $disabledMethodCalls;

    /**
     * @param array<array{context: string, disabled: string}> $disabledMethodCalls
     */
    public function __construct(array $disabledMethodCalls)
    {
        $this->disabledMethodCalls = $disabledMethodCalls;
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
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return [];
        }

        $className = $classReflection->getName();
        $sourceClassType = new ObjectType($className);

        $errors = [];
        foreach ($this->disabledMethodCalls as $disabledMethodCall) {
            $context = $disabledMethodCall['context'];
            [$contextClass, $contextMethod] = explode('::', $context, 2);

            $disabled = $disabledMethodCall['disabled'];
            [$disabledClass, $disabledMethod] = explode('::', $disabled, 2);

            if (!$sourceClassType->isInstanceOf($contextClass)->yes()) {
                continue;
            }

            if ($scope->getFunctionName() !== $contextMethod) {
                continue;
            }

            $callerType = $scope->getType($node->var);
            if (!(new ObjectType($disabledClass))->isSuperTypeOf($callerType)->yes()) {
                continue;
            }

            $methodName = $this->getNameValue($node->name, $scope);
            if ($methodName !== $disabledMethod) {
                continue;
            }

            $file = $scope->getFile();
            $callerClassName = implode('|', $callerType->getObjectClassNames());
            $errors[] = RuleErrorBuilder::message('Calling method ' . $callerClassName . '::' . $methodName . '() in ' . $className . '::' . $scope->getFunctionName() . '() is forbidden.')
                ->identifier('efabrica.disabledMethodCallInContext')
                ->file($file)
                ->line($node->getLine())
                ->build();
        }
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
        $constantStrings = $scope->getType($name)->getConstantStrings();
        if (count($constantStrings) === 1) {
            return $constantStrings[0]->getValue();
        }
        return null;
    }
}

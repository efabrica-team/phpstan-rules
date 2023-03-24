<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\General;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\VerbosityLevel;
use Throwable;

/**
 * @implements Rule<MethodCall>
 */
final class RequiredParametersInMethodCallRule implements Rule
{
    /** @var array<array{context: string, parameters: array<array{name: string, type: string, tip: ?string}>}> */
    private array $requiredParametersInMethodCalls;

    private TypeStringResolver $typeStringResolver;

    /**
     * @param array<array{context: string, parameters: array<array{name: string, type: string, tip: ?string}>}> $requiredParametersInMethodCalls
     */
    public function __construct(array $requiredParametersInMethodCalls, TypeStringResolver $typeStringResolver)
    {
        $this->requiredParametersInMethodCalls = $requiredParametersInMethodCalls;
        $this->typeStringResolver = $typeStringResolver;
    }

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();

        if (!$node->name instanceof Identifier) {
            return [];
        }
        $methodName = $node->name->name;

        $callerType = $scope->getType($node->var);
        if (!$callerType instanceof ObjectType) {
            return [];
        }

        try {
            $methodReflection = $callerType->getMethod($methodName, $scope);
        } catch (Throwable $e) {
            return [];
        }

        $parametersAcceptor = $methodReflection->getVariants()[0] ?? null;
        if ($parametersAcceptor === null) {
            return [];
        }

        $parametersReflections = $parametersAcceptor->getParameters();

        $methodParameters = [];
        foreach ($parametersReflections as $parameterReflection) {
            $methodParameters[] = $parameterReflection->getName();
        }

        $args = $node->getArgs();

        $callArgs = [];
        foreach ($args as $index => $arg) {
            if ($arg->name !== null) {
                $callArgs[$arg->name->name] = $scope->getType($arg->value);
                continue;
            }

            $parameterName = $methodParameters[$index] ?? null;
            if ($parameterName === null) {
                continue;
            }
            $callArgs[$parameterName] = $scope->getType($arg->value);
        }

        $errors = [];
        foreach ($this->requiredParametersInMethodCalls as $requiredParametersInMethodCall) {
            $context = $requiredParametersInMethodCall['context'];
            [$contextClass, $contextMethod] = explode('::', $context, 2);

            if (!$callerType->isInstanceOf($contextClass)->yes()) {
                continue;
            }

            if ($methodName !== $contextMethod) {
                continue;
            }

            $requiredParameters = $requiredParametersInMethodCall['parameters'];
            foreach ($requiredParameters as $requiredParameter) {
                $parameterName = $requiredParameter['name'];
                $requiredType = $this->typeStringResolver->resolve($requiredParameter['type']);
                $tip = $requiredParameter['tip'] ?? null;
                $calledType = $callArgs[$parameterName] ?? null;
                if ($calledType === null) {
                    $error = RuleErrorBuilder::message("Parameter '$parameterName' of method $contextClass::$contextMethod() is required to be {$requiredType->describe(VerbosityLevel::typeOnly())}, none given.")->file($file)->line($node->getLine());
                    if ($tip !== null) {
                        $error->tip($tip);
                    }
                    $errors[] = $error->build();
                    continue;
                }
                if (!$requiredType->accepts($calledType, true)->yes()) {
                    $error = RuleErrorBuilder::message("Parameter '$parameterName' of method $contextClass::$contextMethod() is required to be {$requiredType->describe(VerbosityLevel::typeOnly())}, {$calledType->describe(VerbosityLevel::typeOnly())} given.")->file($file)->line($node->getLine());
                    if ($tip !== null) {
                        $error->tip($tip);
                    }
                    $errors[] = $error->build();
                }
            }
        }
        return $errors;
    }
}

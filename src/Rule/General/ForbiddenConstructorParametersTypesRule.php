<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\General;

use Efabrica\PHPStanRules\Resolver\NameResolver;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\VerbosityLevel;

/**
 * @implements Rule<ClassMethod>
 */
final class ForbiddenConstructorParametersTypesRule implements Rule
{
    /** @var array<array{context: string, forbiddenTypes: array<array{type: string, tip: ?string}>}> */
    private array $forbiddenConstructorParametersTypes;

    private NameResolver $nameResolver;

    private TypeStringResolver $typeStringResolver;

    /**
     * @param array<array{context: string, forbiddenTypes: array<array{type: string, tip: ?string}>}> $forbiddenConstructorParametersTypes
     */
    public function __construct(array $forbiddenConstructorParametersTypes, NameResolver $nameResolver, TypeStringResolver $typeStringResolver)
    {
        $this->forbiddenConstructorParametersTypes = $forbiddenConstructorParametersTypes;
        $this->nameResolver = $nameResolver;
        $this->typeStringResolver = $typeStringResolver;
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();

        if ($node->name->name !== '__construct') {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (!$classReflection instanceof ClassReflection) {
            return [];
        }

        $className = $classReflection->getName();
        $classType = $this->typeStringResolver->resolve($className);
        if (!$classType instanceof ObjectType) {
            return [];
        }

        $methodParameters = [];
        foreach ($node->params as $i => $param) {
            $paramType = $this->nameResolver->resolve($param->type);
            if ($paramType === null) {
                continue;
            }
            $paramName = $this->nameResolver->resolve($param->var);
            $methodParameters[$paramName === null ? ('#' . ($i + 1)) : ('$' . $paramName)] = $this->typeStringResolver->resolve($paramType);
        }

        $errors = [];
        foreach ($this->forbiddenConstructorParametersTypes as $forbiddenConstructorParametersType) {
            $context = $forbiddenConstructorParametersType['context'];
            if (!$classType->isInstanceOf($context)->yes()) {
                continue;
            }

            foreach ($forbiddenConstructorParametersType['forbiddenTypes'] as $forbiddenType) {
                $forbiddenParamType = $this->typeStringResolver->resolve($forbiddenType['type']);
                $tip = $forbiddenType['tip'] ?? null;
                foreach ($methodParameters as $paramName => $methodParameter) {
                    if (!$forbiddenParamType->accepts($methodParameter, true)->yes()) {
                        continue;
                    }
                    $error = RuleErrorBuilder::message("Constructor parameter $paramName of class $className has forbidden type {$methodParameter->describe(VerbosityLevel::typeOnly())}.")->file($file)->line($node->getLine());
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

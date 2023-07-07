<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\General;

use Efabrica\PHPStanRules\Resolver\NameResolver;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use ReflectionException;
use ReflectionMethod;

/**
 * @implements Rule<Concat>
 */
final class DisabledConcatenationWithTranslatedStringsRule implements Rule
{
    /** @var string[] */
    private array $functionCalls = [];

    /** @var array<class-string, string[]> */
    private array $methodCalls = [];

    /** @var array<class-string, string[]> */
    private array $staticCalls = [];

    private NameResolver $nameResolver;

    /**
     * @param string[] $translateCalls
     */
    public function __construct(array $translateCalls, NameResolver $nameResolver)
    {
        foreach ($translateCalls as $translateCall) {
            if (!str_contains($translateCall, '::')) {
                $this->functionCalls[] = $translateCall;
                continue;
            }
            /** @var class-string $class */
            [$class, $method] = explode('::', $translateCall, 2);
            try {
                $reflectionMethod = new ReflectionMethod($class, $method);
            } catch (ReflectionException $e) {
                continue;
            }

            if ($reflectionMethod->isStatic()) {
                if (!isset($this->staticCalls[$class])) {
                    $this->staticCalls[$class] = [];
                }
                $this->staticCalls[$class][] = $method;
            } else {
                if (!isset($this->methodCalls[$class])) {
                    $this->methodCalls[$class] = [];
                }
                $this->methodCalls[$class][] = $method;
            }
        }

        $this->nameResolver = $nameResolver;
    }

    public function getNodeType(): string
    {
        return Concat::class;
    }

    /**
     * @param Concat $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->functionCalls === [] && $this->methodCalls === [] && $this->staticCalls === []) {
            return [];
        }

        $errors = [];
        if ($this->isTranslateCall($node->left, $scope) || $this->isTranslateCall($node->right, $scope)) {
            $errors[] = RuleErrorBuilder::message('Do not concatenate translated strings.')->tip('Every language has its own word ordering, use variables instead.')->build();
        }
        return $errors;
    }

    private function isTranslateCall(Expr $expr, Scope $scope): bool
    {
        if ($expr instanceof FuncCall) {
            $functionName = $this->nameResolver->resolve($expr->name);
            return in_array($functionName, $this->functionCalls, true);
        }

        if ($expr instanceof MethodCall) {
            $callerType = $scope->getType($expr->var);
            $methodName = $this->nameResolver->resolve($expr->name);

            foreach ($this->methodCalls as $class => $methods) {
                if (in_array($methodName, $methods, true) && (new ObjectType($class))->isSuperTypeOf($callerType)->yes()) {
                    return true;
                }

            }
            return false;
        }

        if ($expr instanceof StaticCall) {
            $className = $this->nameResolver->resolve($expr->class);
            if ($className === null) {
                return false;
            }

            $callerType = new ObjectType($className);
            $methodName = $this->nameResolver->resolve($expr->name);

            foreach ($this->staticCalls as $class => $methods) {
                if (in_array($methodName, $methods, true) && (new ObjectType($class))->isSuperTypeOf($callerType)->yes()) {
                    return true;
                }
            }
            return false;
        }

        return false;
    }
}

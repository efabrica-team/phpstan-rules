<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\Performance;

use Efabrica\PHPStanRules\Resolver\NameResolver;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use PhpParser\Node\Expr\BinaryOp\LogicalAnd;
use PhpParser\Node\Expr\BinaryOp\LogicalOr;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\If_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\VerbosityLevel;

/**
 * @implements Rule<If_>
 */
final class CheckSlowCallsInConditionsRule implements Rule
{
    private const FUNCTION_CALL = 'function_call';
    private const METHOD_CALL = 'method_call';
    private const STATIC_METHOD_CALL = 'static_method_call';

    /** @var array{function_call: string[], method_call: array<string, string[]>, static_method_call: array<string, string[]>} */
    private array $conditionSlowCalls = [
        self::FUNCTION_CALL => [],
        self::METHOD_CALL => [],
        self::STATIC_METHOD_CALL => [],
    ];

    private NameResolver $nameResolver;

    /**
     * @param string[] $conditionSlowCalls
     */
    public function __construct(array $conditionSlowCalls, NameResolver $nameResolver)
    {
        foreach ($conditionSlowCalls as $conditionSlowCall) {
            if (str_contains($conditionSlowCall, '->')) {
                [$class, $method] = explode('->', $conditionSlowCall, 2);
                $this->conditionSlowCalls[self::METHOD_CALL][$class] ??= [];
                $this->conditionSlowCalls[self::METHOD_CALL][$class][] = $method;
            } elseif (str_contains($conditionSlowCall, '::')) {
                [$class, $method] = explode('::', $conditionSlowCall, 2);
                $this->conditionSlowCalls[self::STATIC_METHOD_CALL][$class] ??= [];
                $this->conditionSlowCalls[self::STATIC_METHOD_CALL][$class][] = $method;
            } else {
                $this->conditionSlowCalls[self::FUNCTION_CALL][] = $conditionSlowCall;
            }
        }

        $this->nameResolver = $nameResolver;
    }

    public function getNodeType(): string
    {
        return If_::class;
    }

    /**
     * @param If_ $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $errors = $this->processExpr($node->cond, $scope);
        foreach ($node->elseifs as $elseif) {
            $errors = array_merge($this->processExpr($elseif->cond, $scope));
        }
        return $errors;
    }

    /**
     * @return RuleError[]
     */
    private function processExpr(Expr $expr, Scope $scope): array
    {
        $expressions = $this->getConditionExprList($expr, $scope);

        $errors = [];

        $slowCalls = [];
        foreach ($expressions as $expression) {
            if ($expression instanceof CallLike) {
                $name = $this->getCallName($expression, $scope);
                if ($this->isSlow($name)) {
                    $slowCalls[] = $name;
                    continue;
                }
            }

            foreach ($slowCalls as $slowCall) {
                $errors[] = RuleErrorBuilder::message('Performance: Slow call "' . $slowCall . '()" is called in condition before faster expressions. Move it to the end.')->line($expr->getLine())->build();
            }

            $slowCalls = [];
        }

        return $errors;
    }

    /**
     * @return Expr[]
     */
    private function getConditionExprList(Expr $expr, Scope $scope): array
    {
        $expressions = [];
        if ($expr instanceof BooleanAnd || $expr instanceof BooleanOr || $expr instanceof LogicalAnd || $expr instanceof LogicalOr) {
            return array_merge($this->getConditionExprList($expr->left, $scope), $expressions, $this->getConditionExprList($expr->right, $scope));
        }
        if ($expr instanceof BooleanNot) {
            return $this->getConditionExprList($expr->expr, $scope);
        }

        $expressions[] = $expr;
        return $expressions;
    }

    private function isSlow(?string $callName): bool
    {
        if ($callName === null) {
            return false;
        }

        if (str_contains($callName, '->')) {
            return $this->isSlowMethod($callName, '->', $this->conditionSlowCalls[self::METHOD_CALL]);
        } elseif (str_contains($callName, '::')) {
            return $this->isSlowMethod($callName, '::', $this->conditionSlowCalls[self::STATIC_METHOD_CALL]);
        } else {
            foreach ($this->conditionSlowCalls[self::FUNCTION_CALL] as $functionPattern) {
                $functionPattern = $this->createPattern($functionPattern);
                if (preg_match($functionPattern, $callName) === 1) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param non-empty-string $separator
     * @param array<string, string[]> $slowMethodCalls
     */
    private function isSlowMethod(string $callName, string $separator, array $slowMethodCalls): bool
    {
        [$class, $method] = explode($separator, $callName, 2);
        $caller = new ObjectType($class);

        foreach ($slowMethodCalls as $methodCallClass => $methodPatterns) {
            if (!$caller->isInstanceOf($methodCallClass)->yes()) {
                continue;
            }

            foreach ($methodPatterns as $methodPattern) {
                $methodPattern = $this->createPattern($methodPattern);
                if (preg_match($methodPattern, $method) === 1) {
                    return true;
                }
            }
        }
        return false;
    }

    private function createPattern(string $slowCall): string
    {
        return '/' . str_replace('*', '(.*?)', $slowCall) . '/';
    }

    private function getCallName(CallLike $call, Scope $scope): ?string
    {
        if ($call instanceof FuncCall) {
            return $this->nameResolver->resolve($call);
        }
        if ($call instanceof StaticCall) {
            return $this->nameResolver->resolve($call->class) . '::' . $this->nameResolver->resolve($call->name);
        }
        if ($call instanceof MethodCall) {
            $scope->getType($call->var)->describe(VerbosityLevel::typeOnly()) . '->' . $this->nameResolver->resolve($call->name);
        }
        return null;
    }
}

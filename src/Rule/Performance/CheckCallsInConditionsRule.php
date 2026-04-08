<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\Performance;

use Efabrica\PHPStanRules\Resolver\NameResolver;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use PhpParser\Node\Expr\BinaryOp\LogicalAnd;
use PhpParser\Node\Expr\BinaryOp\LogicalOr;
use PhpParser\Node\Expr\BinaryOp\LogicalXor;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\If_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\VerbosityLevel;
use function array_merge;
use function explode;
use function in_array;
use function is_array;
use function preg_match;
use function str_contains;
use function str_replace;

/**
 * @implements Rule<If_>
 */
final class CheckCallsInConditionsRule implements Rule
{
    private const FUNCTION_CALL = 'function_call';
    private const METHOD_CALL = 'method_call';
    private const STATIC_METHOD_CALL = 'static_method_call';

    private bool $considerAllCallsAsSlow;

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
        $this->considerAllCallsAsSlow = $conditionSlowCalls === [];
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
            $errors = array_merge($errors, $this->processExpr($elseif->cond, $scope));
        }
        return $errors;
    }

    /**
     * @return RuleError[]
     */
    private function processExpr(Expr $expr, Scope $scope): array
    {
        $errors = [];
        $conditionParts = $this->splitConditionByBooleanOperators($expr);

        $slowCallsInPreviousParts = [];
        foreach ($conditionParts as $conditionPart) {
            $slowCallsInCurrentPart = $this->getSlowCallsInExpr($conditionPart, $scope);
            if ($slowCallsInCurrentPart !== []) {
                $slowCallsInPreviousParts = array_merge($slowCallsInPreviousParts, $slowCallsInCurrentPart);
                continue;
            }

            $fasterExpression = $this->describeExpression($conditionPart);
            foreach ($slowCallsInPreviousParts as $slowCall) {
                $errors[] = RuleErrorBuilder::message('Performance: "' . $slowCall . '()" is called in condition before faster expression "' . $fasterExpression . '".')
                    ->tip('Move faster expressions to the beginning of the condition and calls to the end.')
                    ->line($expr->getLine())
                    ->build();
            }

            $slowCallsInPreviousParts = [];
        }

        return $errors;
    }

    /**
     * @return Expr[]
     */
    private function splitConditionByBooleanOperators(Expr $expr): array
    {
        if ($expr instanceof BooleanNot) {
            return $this->splitConditionByBooleanOperators($expr->expr);
        }
        if ($expr instanceof Assign) {
            return $this->splitConditionByBooleanOperators($expr->expr);
        }
        if ($expr instanceof Instanceof_) {
            return $this->splitConditionByBooleanOperators($expr->expr);
        }

        if ($expr instanceof BinaryOp) {
            if ($expr instanceof BooleanAnd || $expr instanceof BooleanOr || $expr instanceof LogicalAnd || $expr instanceof LogicalOr || $expr instanceof LogicalXor) {
                return array_merge($this->splitConditionByBooleanOperators($expr->left), $this->splitConditionByBooleanOperators($expr->right));
            }
        }

        return [$expr];
    }

    /**
     * @return string[]
     */
    private function getSlowCallsInExpr(Expr $expr, Scope $scope): array
    {
        $slowCalls = [];
        foreach ($this->collectCallLikes($expr) as $callLike) {
            $callName = $this->getCallName($callLike, $scope);
            if ($callName === null) {
                continue;
            }
            if ($this->isSlow($callName)) {
                $slowCalls[] = $callName;
            }
        }

        return $slowCalls;
    }

    /**
     * @return CallLike[]
     */
    private function collectCallLikes(Expr $expr): array
    {
        if ($expr instanceof CallLike) {
            return [$expr];
        }
        if ($expr instanceof BooleanNot) {
            return $this->collectCallLikes($expr->expr);
        }
        if ($expr instanceof Assign) {
            return $this->collectCallLikes($expr->expr);
        }
        if ($expr instanceof Instanceof_) {
            return $this->collectCallLikes($expr->expr);
        }

        $calls = [];
        foreach ($expr->getSubNodeNames() as $subNodeName) {
            $subNode = $expr->$subNodeName;
            if ($subNode instanceof Expr) {
                $calls = array_merge($calls, $this->collectCallLikes($subNode));
                continue;
            }
            if (!is_array($subNode)) {
                continue;
            }
            foreach ($subNode as $subNodeItem) {
                if ($subNodeItem instanceof Expr) {
                    $calls = array_merge($calls, $this->collectCallLikes($subNodeItem));
                }
            }
        }

        return $calls;
    }

    private function describeExpression(Expr $expr): string
    {
        if ($expr instanceof Variable) {
            $variableName = $this->nameResolver->resolve($expr);
            return $variableName !== null ? '$' . $variableName : '$variable';
        }

        if ($expr instanceof PropertyFetch) {
            return $this->describeExpression($expr->var) . '->' . ($this->nameResolver->resolve($expr->name) ?? 'property');
        }

        if ($expr instanceof StaticPropertyFetch) {
            return ($this->nameResolver->resolve($expr->class) ?? 'ClassName') . '::$' . ($this->nameResolver->resolve($expr->name) ?? 'property');
        }

        if ($expr instanceof ClassConstFetch) {
            return ($this->nameResolver->resolve($expr->class) ?? 'ClassName') . '::' . ($this->nameResolver->resolve($expr->name) ?? 'CONST');
        }

        if ($expr instanceof BooleanNot) {
            return '!' . $this->describeExpression($expr->expr);
        }

        if ($expr instanceof BinaryOp) {
            return 'expression';
        }

        return 'expression';
    }

    private function isSlow(?string $callName): bool
    {
        if ($callName === null) {
            return false;
        }

        if (in_array($callName, ['array_key_exists', 'is_array', 'in_array'], true)) {
            return false;
        }

        if ($this->considerAllCallsAsSlow) {
            return true;
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
        return '/^' . str_replace('*', '(.*?)', $slowCall) . '$/';
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
            return $scope->getType($call->var)->describe(VerbosityLevel::value()) . '->' . $this->nameResolver->resolve($call->name);
        }
        return null;
    }
}

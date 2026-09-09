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
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use function explode;
use function implode;
use function in_array;
use function preg_match;
use function str_contains;

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

    /** @var string[] */
    private array $allowedTranslateConcatenationPatterns;

    private NameResolver $nameResolver;

    /**
     * @param string[] $translateCalls
     * @param string[] $allowedTranslateConcatenationPatterns
     */
    public function __construct(array $translateCalls, array $allowedTranslateConcatenationPatterns, NameResolver $nameResolver, ReflectionProvider $reflectionProvider)
    {
        foreach ($translateCalls as $translateCall) {
            if (!str_contains($translateCall, '::')) {
                $this->functionCalls[] = $translateCall;
                continue;
            }
            /** @var class-string $class */
            [$class, $method] = explode('::', $translateCall, 2);
            if (!$reflectionProvider->hasClass($class)) {
                continue;
            }
            $classReflection = $reflectionProvider->getClass($class);
            if (!$classReflection->hasNativeMethod($method)) {
                continue;
            }

            if ($classReflection->getNativeMethod($method)->isStatic()) {
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

        $this->allowedTranslateConcatenationPatterns = $allowedTranslateConcatenationPatterns;
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
        if (($this->isTranslateCall($node->left, $scope) && !$this->isAllowedToConcatenate($node->right)) ||
            ($this->isTranslateCall($node->right, $scope) && !$this->isAllowedToConcatenate($node->left))) {
            $errors[] = RuleErrorBuilder::message('Do not concatenate translated strings.')
                ->identifier('efabrica.concatenationWithTranslatedStrings')
                ->tip('Every language has its own word ordering, use variables in translations instead, e.g. Hello %name%.')
                ->build();
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

    private function isAllowedToConcatenate(Expr $expr): bool
    {
        if (!$expr instanceof String_) {
            return false;
        }

        return (bool)preg_match('#' . implode('|', $this->allowedTranslateConcatenationPatterns) . '#', $expr->value);
    }
}

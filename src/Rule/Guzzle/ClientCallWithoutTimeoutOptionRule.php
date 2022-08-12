<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\Guzzle;

use PhpParser\ConstExprEvaluator;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ConstantScalarType;
use PHPStan\Type\ObjectType;
use ReflectionClass;

/**
 * @implements Rule<InClassNode>
 */
final class ClientCallWithoutTimeoutOptionRule implements Rule
{
    /**
     * @var array<string, int> method name => position of $options parameter (indexed from 0)
     */
    private array $methodOptionArgPosition = [
        'get' => 1,
        'post' => 1,
        'put' => 1,
        'head' => 1,
        'patch' => 1,
        'delete' => 1,
        'send' => 1,
        'request' => 2,
        'getAsync' => 1,
        'postAsync' => 1,
        'putAsync' => 1,
        'headAsync' => 1,
        'patchAsync' => 1,
        'deleteAsync' => 1,
        'sendAsync' => 1,
        'requestAsync' => 2,
    ];

    private NodeFinder $nodeFinder;

    private ConstExprEvaluator $constExprEvaluator;

    public function __construct(NodeFinder $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
    }

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $inClassNode
     */
    public function processNode(Node $inClassNode, Scope $scope): array
    {
        $file = $scope->getFile();
        $this->constExprEvaluator = new ConstExprEvaluator(fn (Expr $expr) => $this->resolveByNode($inClassNode, $scope, $expr));

        $errors = [];

        /** @var MethodCall[] $methodCallNodes */
        $methodCallNodes = $this->nodeFinder->findInstanceOf([$inClassNode->getOriginalNode()], MethodCall::class);
        foreach ($methodCallNodes as $node) {
            $callerType = $scope->getType($node->var);
            if (!$callerType instanceof ObjectType || !$callerType->isInstanceOf('GuzzleHttp\Client')->yes()) {
                continue;
            }

            if ($node->name instanceof Identifier) {
                $methodName = $node->name->name;
            } else {
                $methodNameType = $scope->getType($node->name);
                if (!$methodNameType instanceof ConstantScalarType) {
                    continue;
                }
                $methodName = $methodNameType->getValue();
            }

            if (!isset($this->methodOptionArgPosition[$methodName])) {
                continue;
            }
            $argPosition = $this->methodOptionArgPosition[$methodName];
            $argAtPosition = $node->getArgs()[$argPosition] ?? null;
            if ($argAtPosition === null) {
                $errors[] = RuleErrorBuilder::message('Method GuzzleHttp\Client::' . $methodName . ' is called without timeout option.')->file($file)->line($node->getStartLine())->build();
                continue;
            }

            $options = $this->constExprEvaluator->evaluateDirectly($argAtPosition->value);
            if (is_array($options)) {
                if (!array_key_exists('timeout', $options)) {
                    $errors[] = RuleErrorBuilder::message('Method GuzzleHttp\Client::' . $methodName . ' is called without timeout option.')->file($file)->line($node->getStartLine())->build();
                }
            }
        }

        return $errors;
    }

    /**
     * @return mixed
     */
    private function resolveByNode(InClassNode $inClassNode, Scope $scope, Expr $expr)
    {
        if ($expr instanceof ClassConstFetch) {
            if (!$expr->class instanceof Name) {
                return null;
            }
            if (!$expr->name instanceof Identifier) {
                return null;
            }
            /** @var class-string $className */
            $className = $expr->class->toString();
            $reflectionClass = new ReflectionClass($className);
            $constantName = $this->getNameValue($expr->name, $scope);
            if ($constantName === null) {
                return null;
            }
            return $reflectionClass->getConstant($constantName);
        }

        if ($expr instanceof PropertyFetch) {
            /** @var PropertyProperty[] $propertyNodes */
            $propertyNodes = $this->nodeFinder->findInstanceOf([$inClassNode->getOriginalNode()], PropertyProperty::class);
            $properties = [];
            foreach ($propertyNodes as $propertyNode) {
                $propertyNodeDefault = null;
                if ($propertyNode->default) {
                    $propertyNodeDefaultType = $scope->getType($propertyNode->default);
                    if ($propertyNodeDefaultType instanceof ConstantScalarType) {
                        $propertyNodeDefault = $propertyNodeDefaultType->getValue();
                    }
                }
                $properties[$propertyNode->name->name] = $propertyNodeDefault;
            }

            $propertyFetchName = $this->getNameValue($expr->name, $scope);
            if ($propertyFetchName === null) {
                return null;
            }

            return $properties[$propertyFetchName] ?? null;
        }

        if ($expr instanceof MethodCall) {
            $methodName = $this->getNameValue($expr->name, $scope);
            if ($methodName === null) {
                return null;
            }
            /** @var ClassMethod[] $classMethodNodes */
            $classMethodNodes = $this->nodeFinder->findInstanceOf([$inClassNode->getOriginalNode()], ClassMethod::class);
            foreach ($classMethodNodes as $classMethodNode) {
                if ($classMethodNode->name->name !== $methodName) {
                    continue;
                }
                /** @var Return_|null $returnStatement */
                $returnStatement = $this->nodeFinder->findFirstInstanceOf($classMethodNode, Return_::class);
                if (!$returnStatement) {
                    return null;
                }
                if ($returnStatement->expr === null) {
                    return null;
                }

                $returnStatementType = $scope->getType($returnStatement->expr);
                if (!$returnStatementType instanceof ConstantScalarType) {
                    return null;
                }

                $value = $returnStatementType->getValue();
                if ($value === null) {
                    $value = $this->constExprEvaluator->evaluateDirectly($returnStatement->expr);
                }
                return $value;
            }
        }

        return null;
    }

    /**
     * @param Identifier|Expr $name
     */
    private function getNameValue($name, Scope $scope): ?string
    {
        if ($name instanceof Identifier) {
            return $name->toString();
        }
        $nameType = $scope->getType($name);
        if ($nameType instanceof ConstantScalarType) {
            return $nameType->getValue();
        }
        return null;
    }
}

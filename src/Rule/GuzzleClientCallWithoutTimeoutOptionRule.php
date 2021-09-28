<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule;

use PhpParser\ConstExprEvaluator;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use Symplify\Astral\NodeValue\NodeValueResolver;

final class GuzzleClientCallWithoutTimeoutOptionRule implements Rule
{
    private NodeFinder $nodeFinder;

    private NodeValueResolver $nodeValueResolver;

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
        'getAsync' => 1,
        'postAsync' => 1,
        'putAsync' => 1,
        'headAsync' => 1,
        'patchAsync' => 1,
        'deleteAsync' => 1,
        'send' => 1,
        'sendAsync' => 1,
        'request' => 2,
        'requestAsync' => 2,
    ];

    private ConstExprEvaluator $constExprEvaluator;

    public function __construct(NodeFinder $nodeFinder, NodeValueResolver $nodeValueResolver)
    {
        $this->nodeFinder = $nodeFinder;
        $this->nodeValueResolver = $nodeValueResolver;
    }

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     */
    public function processNode(Node $inClassNode, Scope $scope): array
    {
        $file = $scope->getFile();
        $methodCallNodes = $this->nodeFinder->findInstanceOf([$inClassNode->getOriginalNode()], MethodCall::class);
        $this->constExprEvaluator = new ConstExprEvaluator(fn (Expr $expr) => $this->resolveByNode($inClassNode, $scope, $expr));

        $errors = [];
        foreach ($methodCallNodes as $node) {
            $callerType = $scope->getType($node->var);
            if (!$callerType instanceof ObjectType || !$callerType->isInstanceOf('GuzzleHttp\Client')->yes()) {
                continue;
            }

            $methodName = $node->name->name;
            if (!isset($this->methodOptionArgPosition[$methodName])) {
                continue;
            }

            $argPosition = $this->methodOptionArgPosition[$methodName];
            $argAtPosition = $node->args[$argPosition] ?? null;
            if ($argAtPosition === null) {
                $errors[] = RuleErrorBuilder::message('Method GuzzleHttp\Client::' . $methodName . ' is called without timeout option')->file($file)->line($node->getStartLine())->build();
            }

            $options = $this->nodeValueResolver->resolveWithScope($argAtPosition->value, $scope);
            if (!is_array($options)) {
                $options = $this->constExprEvaluator->evaluateDirectly($argAtPosition->value);
            }

            if (is_array($options)) {
                if (!array_key_exists('timeout', $options)) {
                    $errors[] = RuleErrorBuilder::message('Method GuzzleHttp\Client::' . $methodName . ' is called without timeout option')->file($file)->line($node->getStartLine())->build();
                }
            }
        }

        return $errors;
    }

    private function resolveByNode(InClassNode $inClassNode, Scope $scope, Expr $expr)
    {
        if ($expr instanceof PropertyFetch) {
            /** @var PropertyProperty[] $propertyNodes */
            $propertyNodes = $this->nodeFinder->findInstanceOf([$inClassNode->getOriginalNode()], PropertyProperty::class);
            $properties = [];
            foreach ($propertyNodes as $propertyNode) {
                $properties[$propertyNode->name->name] = $propertyNode->default ? $this->nodeValueResolver->resolveWithScope($propertyNode->default, $scope) : null;
            }

            if (is_string($expr->name)) {
                $propertyFetchName = $expr->name;
            } elseif ($expr->name instanceof Identifier) {
                $propertyFetchName = $expr->name->name;
            } else {
                $propertyFetchName = $this->nodeValueResolver->resolveWithScope($expr->name, $scope);
            }

            if ($propertyFetchName === null) {
                return null;
            }

            return $properties[$propertyFetchName] ?? null;
        }

        if ($expr instanceof MethodCall) {
            $methodName = $expr->name instanceof Identifier ? $expr->name->name : $this->nodeValueResolver->resolveWithScope($expr->name, $scope);
            if (!$methodName) {
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

                $value = $this->nodeValueResolver->resolveWithScope($returnStatement->expr, $scope);
                if ($value === null) {
                    $value = $this->constExprEvaluator->evaluateDirectly($returnStatement->expr);
                }
                return $value;
            }
        }

        return null;
    }
}

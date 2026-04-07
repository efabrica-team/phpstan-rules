<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\Performance;

use Efabrica\PHPStanRules\Resolver\NameResolver;
use Nette\Database\Table\Selection;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use function array_search;
use function is_array;

/**
 * @implements Rule<MethodCall>
 */
final class UseNetteDatabaseSelectionFetchTogetherWithLimitRule implements Rule
{
    private NameResolver $nameResolver;

    public function __construct(NameResolver $nameResolver)
    {
        $this->nameResolver = $nameResolver;
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
        $callerType = $scope->getType($node->var);
        if (!$callerType instanceof ObjectType) {
            return [];
        }

        if (!$callerType->isInstanceOf(Selection::class)->yes()) {
            return [];
        }

        $methodName = $this->nameResolver->resolve($node->name);
        if ($methodName !== 'fetch') {
            return [];
        }

        if ($this->hasLimitOneInFluentChain($node->var) || $this->hasPreviousLimitOneForVariable($node)) {
            return [];
        }

        return [
            RuleErrorBuilder::message('Use Nette\Database\Selection::fetch() in combination with limit(1)')->build(),
        ];
    }

    private function hasLimitOneInFluentChain(Expr $expr): bool
    {
        if (!$expr instanceof MethodCall) {
            return false;
        }

        $methodName = $this->nameResolver->resolve($expr->name);
        if ($methodName === 'limit') {
            return $this->isLimitOne($expr->getArgs()[0] ?? null);
        }

        return $this->hasLimitOneInFluentChain($expr->var);
    }

    private function hasPreviousLimitOneForVariable(MethodCall $fetchMethodCall): bool
    {
        if (!$fetchMethodCall->var instanceof Variable) {
            return false;
        }

        $variableName = $this->nameResolver->resolve($fetchMethodCall->var);
        if ($variableName === null) {
            return false;
        }

        $previousStatements = $this->findPreviousSiblingStatements($fetchMethodCall);
        foreach ($previousStatements as $previousStatement) {
            if ($this->isLimitOneCallOnVariable($previousStatement, $variableName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Stmt[]
     */
    private function findPreviousSiblingStatements(Node $node): array
    {
        $currentNode = $this->findEnclosingStatement($node);
        if ($currentNode === null) {
            return [];
        }

        while (true) {
            $parentNode = $currentNode->getAttribute('parent');
            if (!$parentNode instanceof Node) {
                return [];
            }

            foreach ($parentNode->getSubNodeNames() as $subNodeName) {
                $subNode = $parentNode->$subNodeName;
                if (!is_array($subNode)) {
                    continue;
                }

                $index = array_search($currentNode, $subNode, true);
                if ($index === false) {
                    continue;
                }

                $previousStatements = [];
                for ($i = 0; $i < $index; $i++) {
                    if ($subNode[$i] instanceof Stmt) {
                        $previousStatements[] = $subNode[$i];
                    }
                }

                return $previousStatements;
            }

            $currentNode = $parentNode;
        }
    }

    private function findEnclosingStatement(Node $node): ?Stmt
    {
        $currentNode = $node;
        while (true) {
            $parentNode = $currentNode->getAttribute('parent');
            if (!$parentNode instanceof Node) {
                return null;
            }

            if ($parentNode instanceof Stmt) {
                return $parentNode;
            }

            $currentNode = $parentNode;
        }
    }

    private function isLimitOneCallOnVariable(Stmt $stmt, string $variableName): bool
    {
        if (!$stmt instanceof Expression) {
            return false;
        }

        if (!$stmt->expr instanceof MethodCall) {
            return false;
        }

        $methodName = $this->nameResolver->resolve($stmt->expr->name);
        if ($methodName !== 'limit') {
            return false;
        }

        if (!$this->isLimitOne($stmt->expr->getArgs()[0] ?? null)) {
            return false;
        }

        return $this->resolveRootVariableName($stmt->expr) === $variableName;
    }

    private function resolveRootVariableName(Expr $expr): ?string
    {
        if ($expr instanceof Variable) {
            return $this->nameResolver->resolve($expr);
        }

        if (!$expr instanceof MethodCall) {
            return null;
        }

        return $this->resolveRootVariableName($expr->var);
    }

    private function isLimitOne(?Arg $arg): bool
    {
        if ($arg === null) {
            return false;
        }

        return $arg->value instanceof Scalar && $arg->value->value === 1;
    }
}

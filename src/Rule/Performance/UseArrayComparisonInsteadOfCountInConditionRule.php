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
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\BinaryOp\GreaterOrEqual;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\LogicalAnd;
use PhpParser\Node\Expr\BinaryOp\LogicalOr;
use PhpParser\Node\Expr\BinaryOp\LogicalXor;
use PhpParser\Node\Expr\BinaryOp\NotEqual;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\BinaryOp\Smaller;
use PhpParser\Node\Expr\BinaryOp\SmallerOrEqual;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Stmt\If_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use function array_merge;

/**
 * @implements Rule<If_>
 */
final class UseArrayComparisonInsteadOfCountInConditionRule implements Rule
{
    private NameResolver $nameResolver;

    public function __construct(NameResolver $nameResolver)
    {
        $this->nameResolver = $nameResolver;
    }

    public function getNodeType(): string
    {
        return If_::class;
    }

    /**
     * @param If_ $node
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $errors = $this->processConditionExpr($node->cond);
        foreach ($node->elseifs as $elseif) {
            $errors = array_merge($errors, $this->processConditionExpr($elseif->cond));
        }

        return $errors;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function processConditionExpr(Expr $expr): array
    {
        if ($expr instanceof BooleanAnd || $expr instanceof BooleanOr || $expr instanceof LogicalAnd || $expr instanceof LogicalOr || $expr instanceof LogicalXor) {
            return array_merge($this->processConditionExpr($expr->left), $this->processConditionExpr($expr->right));
        }

        if ($expr instanceof Assign) {
            return $this->processConditionExpr($expr->expr);
        }

        if ($expr instanceof BooleanNot) {
            if ($this->isCountCall($expr->expr)) {
                return [$this->buildEmptyError($expr->getLine())];
            }

            return $this->processConditionExpr($expr->expr);
        }

        if ($expr instanceof BinaryOp) {
            $error = $this->processCountComparison($expr);
            return $error !== null ? [$error] : [];
        }

        if ($this->isCountCall($expr)) {
            return [$this->buildNonEmptyError($expr->getLine())];
        }

        return [];
    }

    private function processCountComparison(BinaryOp $expr): ?IdentifierRuleError
    {
        if ($this->isCountCall($expr->left) && $expr->right instanceof Int_) {
            return $this->buildComparisonError($expr, $expr, $expr->right->value);
        }

        if ($expr->left instanceof Int_ && $this->isCountCall($expr->right)) {
            return $this->buildComparisonError($expr, $this->swapOperator($expr), $expr->left->value);
        }

        return null;
    }

    private function buildComparisonError(BinaryOp $expr, BinaryOp $operator, int $number): ?IdentifierRuleError
    {
        if ($this->isNonEmptyComparison($operator, $number)) {
            return $this->buildNonEmptyError($expr->getLine());
        }

        if ($this->isEmptyComparison($operator, $number)) {
            return $this->buildEmptyError($expr->getLine());
        }

        return null;
    }

    private function isNonEmptyComparison(BinaryOp $operator, int $number): bool
    {
        if ($operator instanceof Greater && $number === 0) {
            return true;
        }
        if ($operator instanceof GreaterOrEqual && $number === 1) {
            return true;
        }
        if (($operator instanceof NotEqual || $operator instanceof NotIdentical) && $number === 0) {
            return true;
        }

        return false;
    }

    private function isEmptyComparison(BinaryOp $operator, int $number): bool
    {
        if (($operator instanceof Equal || $operator instanceof Identical) && $number === 0) {
            return true;
        }
        if ($operator instanceof SmallerOrEqual && $number === 0) {
            return true;
        }
        if ($operator instanceof Smaller && $number === 1) {
            return true;
        }

        return false;
    }

    private function swapOperator(BinaryOp $operator): BinaryOp
    {
        if ($operator instanceof Greater) {
            return new Smaller($operator->left, $operator->right, $operator->getAttributes());
        }
        if ($operator instanceof GreaterOrEqual) {
            return new SmallerOrEqual($operator->left, $operator->right, $operator->getAttributes());
        }
        if ($operator instanceof Smaller) {
            return new Greater($operator->left, $operator->right, $operator->getAttributes());
        }
        if ($operator instanceof SmallerOrEqual) {
            return new GreaterOrEqual($operator->left, $operator->right, $operator->getAttributes());
        }

        return $operator;
    }

    private function isCountCall(Expr $expr): bool
    {
        if (!$expr instanceof FuncCall) {
            return false;
        }

        return $this->nameResolver->resolve($expr) === 'count';
    }

    private function buildNonEmptyError(int $line): IdentifierRuleError
    {
        return RuleErrorBuilder::message('Use "$array !== []" instead of count() when checking for non-empty array in condition.')
            ->identifier('efabrica.useArrayComparisonInsteadOfCount')
            ->line($line)
            ->build();
    }

    private function buildEmptyError(int $line): IdentifierRuleError
    {
        return RuleErrorBuilder::message('Use "$array === []" instead of count() when checking for empty array in condition.')
            ->identifier('efabrica.useArrayComparisonInsteadOfCount')
            ->line($line)
            ->build();
    }
}

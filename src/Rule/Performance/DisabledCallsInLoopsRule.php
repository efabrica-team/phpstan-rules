<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\Performance;

use Efabrica\PHPStanRules\Resolver\NameResolver;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\While_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<FuncCall>
 */
final class DisabledCallsInLoopsRule implements Rule
{
    /** @var string[]  */
    private array $disabledFunctions = [
        'array_merge',
        'array_merge_recursive',
    ];

    private NameResolver $nameResolver;

    public function __construct(NameResolver $nameResolver)
    {
        $this->nameResolver = $nameResolver;
    }

    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    /**
     * @param FuncCall $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $functionName = $this->nameResolver->resolve($node);
        if (!in_array($functionName, $this->disabledFunctions, true)) {
            return [];
        }

        if (!$this->isInLoop($node)) {
            return [];
        }

        $reassignedVariable = $this->findReassignedVariable($node);
        if ($reassignedVariable === null) {
            return [];
        }

        return [
            RuleErrorBuilder::message("Performance: Do not use \"$functionName\" in loop to reassign variable \"$reassignedVariable\".")->tip('See https://www.exakat.io/en/speeding-up-array_merge/')->build(),
        ];
    }

    private function isInLoop(Node $node): bool
    {
        $parentNode = $node->getAttribute('parent');
        if (!$parentNode instanceof Node) {
            return false;
        }

        if ($parentNode instanceof For_) {
            return true;
        }

        if ($parentNode instanceof Foreach_) {
            return true;
        }

        if ($parentNode instanceof While_) {
            return true;
        }

        if ($parentNode instanceof Do_) {
            return true;
        }

        return $this->isInLoop($parentNode);
    }

    private function findReassignedVariable(FuncCall $funcCall): ?string
    {
        $parentNode = $funcCall->getAttribute('parent');
        if (!$parentNode instanceof Assign) {
            return null;
        }

        if (!$parentNode->var instanceof Variable) {
            return null;
        }

        $assignedVariableName = $this->nameResolver->resolve($parentNode->var);
        if ($assignedVariableName === null) {
            return null;
        }

        $functionParameterVariableNames = [];
        foreach ($funcCall->getArgs() as $arg) {
            if (!$arg->value instanceof Variable) {
                continue;
            }
            $functionParameterVariableNames[] = $this->nameResolver->resolve($arg->value->name);
        }
        $functionParameterVariableNames = array_filter($functionParameterVariableNames);
        return in_array($assignedVariableName, $functionParameterVariableNames, true) ? $assignedVariableName : null;
    }
}

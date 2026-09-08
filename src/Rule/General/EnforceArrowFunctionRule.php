<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\General;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use function count;

/**
 * @implements Rule<Closure>
 */
final class EnforceArrowFunctionRule implements Rule
{
    public function getNodeType(): string
    {
        return Closure::class;
    }

    /**
     * @param Closure $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->stmts === null || count($node->stmts) !== 1) {
            return [];
        }

        $onlyStatement = $node->stmts[0];
        if (!$onlyStatement instanceof Return_) {
            return [];
        }

        if ($onlyStatement->expr === null) {
            return [];
        }

        foreach ($node->uses as $closureUse) {
            if ($closureUse->byRef) {
                return [];
            }
        }

        return [
            RuleErrorBuilder::message('Closure has a single return expression. Use an arrow function instead.')
                ->line($node->getLine())
                ->build(),
        ];
    }
}

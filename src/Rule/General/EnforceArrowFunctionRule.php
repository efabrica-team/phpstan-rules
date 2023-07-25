<?php

namespace Efabrica\PHPStanRules\Rule\General;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

class EnforceArrowFunctionRule implements Rule
{
    public function getNodeType(): string
    {
        return Closure::class;
    }

    /**
     * @param Closure $node
     * @param Scope   $scope
     * @return array|string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->stmts === null || count($node->stmts) !== 1) {
            return [];
        }

        $onlyStatement = reset($node->stmts);
        if (!$onlyStatement instanceof Node\Stmt\Return_) {
            return [];
        }

        return [
            'Closure only has a single return statement. Use an arrow function instead.',
        ];
    }
}

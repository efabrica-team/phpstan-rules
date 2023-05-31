<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\Performance;

use Efabrica\PHPStanRules\Resolver\NameResolver;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<FuncCall>
 */
final class DisabledCallsInLoopsRule implements Rule
{
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

        return [
            RuleErrorBuilder::message('Performance: Do not use "' . $functionName . '" in loop.')->build(),
        ];
    }

    private function isInLoop(Node $node): bool
    {
        $parentNode = $node->getAttribute('parent');
        if ($parentNode === null) {
            return false;
        }

        if ($parentNode instanceof For_) {
            return true;
        }

        if ($parentNode instanceof Foreach_) {
            return true;
        }

        return $this->isInLoop($parentNode);
    }
}

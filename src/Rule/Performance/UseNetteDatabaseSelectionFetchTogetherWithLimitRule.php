<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\Performance;

use Efabrica\PHPStanRules\Resolver\NameResolver;
use Nette\Database\Table\Selection;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\ObjectType;

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


        return [];
    }
}

<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\Tomaj\NetteApi;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use Symplify\Astral\NodeValue\NodeValueResolver;

/**
 * @implements Rule<New_>
 */
final class InputParamNameRule implements Rule
{
    public function __construct(private NodeValueResolver $nodeValueResolver)
    {
    }

    public function getNodeType(): string
    {
        return New_::class;
    }

    /**
     * @param New_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->class instanceof Node\Name) {
            return [];
        }

        $file = $scope->getFile();

        $classType = new ObjectType($node->class->toString());
        if (!$classType->isInstanceOf('Tomaj\NetteApi\Params\InputParam')->yes()) {
            return [];
        }

        $nameArg = $node->getArgs()[0] ?? null;
        if (!$nameArg) {
            return [
                RuleErrorBuilder::message('Missing name of input parameter')->file($file)->line($node->getStartLine())->build()
            ];
        }

        $paramName = $this->nodeValueResolver->resolveWithScope($nameArg->value, $scope);
        if (!is_string($paramName)) {
            return [];
        }

        $recommendedName = str_replace('-', '_', Strings::webalize($paramName, null, false));
        if ($paramName !== $recommendedName) {
            return [
                RuleErrorBuilder::message('Incorrect parameter name "' . $paramName . '". Use "' . $recommendedName . '" instead')->file($file)->line($node->getStartLine())->build()
            ];
        }

        return [];
    }
}

<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\Tomaj\NetteApi;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use function count;
use function is_null;
use function str_replace;

/**
 * @implements Rule<New_>
 */
final class InputParamNameRule implements Rule
{
    public function getNodeType(): string
    {
        return New_::class;
    }

    /**
     * @param New_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->class instanceof Name) {
            return [];
        }

        $file = $scope->getFile();

        $classType = new ObjectType($node->class->toString());
        if (!$classType->isInstanceOf('Tomaj\NetteApi\Params\InputParam')->yes()) {
            return [];
        }

        $nameArg = $node->getArgs()[0] ?? null;
        if (is_null($nameArg)) {
            return [
                RuleErrorBuilder::message('Missing name of input parameter.')
                    ->identifier('efabrica.netteApiInputParamName')
                    ->file($file)
                    ->line($node->getStartLine())
                    ->build(),
            ];
        }

        $constantStrings = $scope->getType($nameArg->value)->getConstantStrings();
        if (count($constantStrings) !== 1) {
            return [];
        }

        $paramName = $constantStrings[0]->getValue();
        $recommendedName = str_replace('-', '_', Strings::webalize($paramName, null, false));
        if ($paramName !== $recommendedName) {
            return [
                RuleErrorBuilder::message('Incorrect parameter name "' . $paramName . '". Use "' . $recommendedName . '" instead.')
                    ->identifier('efabrica.netteApiInputParamName')
                    ->file($file)
                    ->line($node->getStartLine())
                    ->build(),
            ];
        }

        return [];
    }
}

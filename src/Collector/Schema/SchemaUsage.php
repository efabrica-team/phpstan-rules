<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Collector\Schema;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use function json_encode;
use function strpos;

/**
 * @implements Collector<New_, array{string, string, int}>
 */
final class SchemaUsage implements Collector
{
    public function getNodeType(): string
    {
        return New_::class;
    }

    public function processNode(Node $node, Scope $scope)
    {
        if (!$node->class instanceof Name) {
            return null;
        }
        $className = $node->class->toCodeString();
        if (strpos($className, '\\Schema\\') === false) {
             return null;
        }

        $params = [];
        foreach ($node->getArgs() as $key => $arg) {
            $params[] = [
                'key' => $key,
                'name' => $arg->name !== null ? $arg->name->name : null,
                'value' => ValueNormalizer::fromExpr($arg->value),
                'unpack' => $arg->unpack,
            ];
        }
        if (empty($params)) {
            return null;
        }
        return [$className, (string) json_encode($params), $node->getLine()];
    }
}

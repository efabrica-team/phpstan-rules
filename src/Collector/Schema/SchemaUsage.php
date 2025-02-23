<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Collector\Schema;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

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
            $tmp = [];
            if (!empty($arg->name)) {
                $tmp['name'] = $arg->name->name;
            }
            $tmp['key'] = $key;
            $tmp['type'] = get_class($arg->value);
            if ($arg->value instanceof Array_) {
                $tmp['aditional'] = count($arg->value->items);
            }
            if (strpos($tmp['type'], 'Scalar') !== false && property_exists($arg->value, 'value') && (is_numeric($arg->value->value) || is_string($arg->value->value))) {
                $tmp['aditional'] = $arg->value->value;
            }

            $params[] = $tmp;
        }
        if (empty($params)) {
            return null;
        }
        return [$node->class->toCodeString(), (string) json_encode($params), $node->getLine()];
    }
}

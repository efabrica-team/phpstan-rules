<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Collector\Schema;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\UnaryMinus;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use ReflectionException;
use ReflectionParameter;
use function count;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function strrpos;
use function strtolower;
use function substr;

/**
 * Converts static values from AST and from reflection into one comparable string form,
 * so that an argument passed in `new` can be compared with a constructor parameter default.
 */
final class ValueNormalizer
{
    /**
     * @return string|null normalized value, null when the expression is not a static value
     */
    public static function fromExpr(Expr $expr): ?string
    {
        if ($expr instanceof ConstFetch) {
            return self::fromConstantName($expr->name->getLast());
        }
        if ($expr instanceof ClassConstFetch) {
            if (!$expr->name instanceof Identifier) {
                return null;
            }
            if (strtolower($expr->name->toString()) === 'class') {
                return $expr->class instanceof Name ? self::fromValue($expr->class->toString()) : null;
            }
            return self::fromConstantName($expr->name->toString());
        }
        if ($expr instanceof String_ || $expr instanceof LNumber || $expr instanceof DNumber) {
            return self::fromValue($expr->value);
        }
        if ($expr instanceof UnaryMinus && ($expr->expr instanceof LNumber || $expr->expr instanceof DNumber)) {
            return self::fromValue(-$expr->expr->value);
        }
        if ($expr instanceof Array_) {
            return count($expr->items) === 0 ? self::fromValue([]) : null;
        }
        return null;
    }

    /**
     * @return string|null normalized default value, null when parameter has no default or default is not a static value
     */
    public static function fromParameter(ReflectionParameter $parameter): ?string
    {
        if (!$parameter->isDefaultValueAvailable()) {
            return null;
        }
        try {
            if ($parameter->isDefaultValueConstant()) {
                $name = (string) $parameter->getDefaultValueConstantName();
                $separator = strrpos($name, '::');
                if ($separator === false) {
                    $separator = strrpos($name, '\\');
                }
                if ($separator !== false) {
                    $name = substr($name, $separator + ($name[$separator] === ':' ? 2 : 1));
                }
                return self::fromConstantName($name);
            }
            return self::fromValue($parameter->getDefaultValue());
        } catch (ReflectionException $e) {
            return null;
        }
    }

    /**
     * @param mixed $value
     */
    public static function fromValue($value): ?string
    {
        if ($value === null) {
            return 'null';
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_int($value)) {
            return 'int:' . $value;
        }
        if (is_float($value)) {
            return 'float:' . $value;
        }
        if (is_string($value)) {
            return 'string:' . $value;
        }
        if (is_array($value)) {
            return 'array:' . count($value);
        }
        return null;
    }

    private static function fromConstantName(string $name): string
    {
        $lower = strtolower($name);
        if ($lower === 'true' || $lower === 'false' || $lower === 'null') {
            return $lower;
        }
        return 'const:' . $name;
    }
}

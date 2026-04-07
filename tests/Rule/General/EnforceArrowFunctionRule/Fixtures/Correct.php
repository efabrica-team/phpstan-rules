<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\EnforceArrowFunctionRule\Fixtures;

final class Correct
{
    /**
     * @param int[] $numbers
     * @return int[]
     */
    public function map(array $numbers): array
    {
        return array_map(fn (int $number): int => $number * 2, $numbers);
    }

    /**
     * @param int[] $numbers
     * @return int[]
     */
    public function mapWithMultipleStatements(array $numbers): array
    {
        return array_map(function (int $number): int {
            $number = $number * 2;
            return $number;
        }, $numbers);
    }

    /**
     * @param int[] $numbers
     * @return int[]
     */
    public function mapWithReferenceUse(array $numbers): array
    {
        $multiplier = 2;
        return array_map(function (int $number) use (&$multiplier): int {
            return $number * $multiplier;
        }, $numbers);
    }

    public function closureWithoutReturnExpr(): ?int
    {
        $closure = function () {
            return;
        };

        return $closure();
    }
}

<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\EnforceArrowFunctionRule\Fixtures;

final class Wrong
{
    /**
     * @param int[] $numbers
     * @return int[]
     */
    public function map(array $numbers): array
    {
        return array_map(function (int $number): int {
            return $number * 2;
        }, $numbers);
    }

    /**
     * @param int[] $numbers
     * @return int[]
     */
    public function filter(array $numbers): array
    {
        return array_filter($numbers, static function (int $number): bool {
            return $number > 10;
        });
    }
}

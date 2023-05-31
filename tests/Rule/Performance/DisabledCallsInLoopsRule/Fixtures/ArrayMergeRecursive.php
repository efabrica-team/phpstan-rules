<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Performance\DisabledCallsInLoopsRule\Fixtures;

final class ArrayMergeRecursive
{
    public function callArrayMergeInFor(array $data): array
    {
        $result = [];
        for ($i = 0; $i < 100; $i++) {
            $result = array_merge_recursive($result, $data[$i]);
        }
        return $result;
    }

    public function callArrayMergeInForeach(array $data): array
    {
        $result = [];
        foreach ($data as $row) {
            $result = array_merge_recursive($result, $row);
        }
        return $result;
    }

    public function callArrayMergeOnce(array $data): array
    {
        return array_merge_recursive([], ...$data);
    }
}

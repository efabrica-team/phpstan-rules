<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Performance\DisabledCallsInLoopsRule\Fixtures;

final class ArrayMergeRecursive
{
    public function callArrayMergeRecursiveInFor(array $data): array
    {
        $result = [];
        for ($i = 0; $i < 100; $i++) {
            $result = array_merge_recursive($result, $data[$i]);
        }
        return $result;
    }

    public function callArrayMergeRecursiveInForeach(array $data): array
    {
        $result = [];
        foreach ($data as $row) {
            $result = array_merge_recursive($result, $row);
        }
        return $result;
    }

    public function callArrayMergeRecursiveInWhile(array $data): array
    {
        $result = [];
        $i = 0;
        while (isset($data[$i])) {
            $result = array_merge_recursive($result, $data[$i]);
            $i++;
        }
        return $result;
    }

    public function callArrayMergeRecursiveInDoWhile(array $data): array
    {
        $result = [];
        $i = 0;
        do {
            $result = array_merge_recursive($result, $data[$i]);
            $i++;
        } while (isset($data[$i]));
        return $result;
    }

    public function callArrayMergeRecursiveOnce(array $data): array
    {
        return array_merge_recursive([], ...$data);
    }
}

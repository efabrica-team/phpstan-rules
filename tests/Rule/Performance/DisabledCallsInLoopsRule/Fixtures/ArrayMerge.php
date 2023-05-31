<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Performance\DisabledCallsInLoopsRule\Fixtures;

final class ArrayMerge
{
    public function callArrayMergeInFor(array $data): array
    {
        $result = [];
        for ($i = 0; $i < 100; $i++) {
            $result = array_merge($result, $data[$i]);
        }
        return $result;
    }

    public function callArrayMergeInForeach(array $data): array
    {
        $result = [];
        foreach ($data as $row) {
            $result = array_merge($result, $row);
        }
        return $result;
    }

    public function callArrayMergeInWhile(array $data): array
    {
        $result = [];
        $i = 0;
        while (isset($data[$i])) {
            $result = array_merge($result, $data[$i]);
            $i++;
        }
        return $result;
    }

    public function callArrayMergeInDoWhile(array $data): array
    {
        $result = [];
        $i = 0;
        do {
            $result = array_merge($result, $data[$i]);
            $i++;
        } while (isset($data[$i]));
        return $result;
    }

    public function callArrayMergeOnce(array $data): array
    {
        return array_merge([], ...$data);
    }
}

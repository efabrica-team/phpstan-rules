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
            $result = array_merge_recursive($row, $result);
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
            $result = array_merge_recursive($data[$i], $result);
            $i++;
        } while (isset($data[$i]));
        return $result;
    }

    public function callArrayMergeRecursiveOnce(array $data): array
    {
        return array_merge_recursive([], ...$data);
    }

    public function arrayMergeRecursiveInForeachWithoutAssign(array $defaultParams, array $allParams): array
    {
        return array_merge_recursive($defaultParams, $allParams);
    }

    public function arrayMergeRecursiveWithAssignToAnotherVariable(array $defaultParams, array $allParams): array
    {
        $paramsList = [];
        foreach ($allParams as $oneParams) {
            $paramsList[] = array_merge_recursive($defaultParams, $oneParams);
        }
        return $paramsList;
    }

    public function arrayMergeRecursiveDirectlyInForeach(array $defaultParams, array $otherParams): array
    {
        $params = [];
        foreach (array_merge_recursive($defaultParams, $otherParams) as $param) {
            $params[] = $param;
        }
        return $params;
    }
}

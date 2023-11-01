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
            $result = array_merge($row, $result);
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
            $result = array_merge($data[$i], $result);
            $i++;
        } while (isset($data[$i]));
        return $result;
    }

    public function callArrayMergeOnce(array $data): array
    {
        return array_merge([], ...$data);
    }

    public function arrayMergeInForeachWithoutAssign(array $defaultParams, array $allParams): array
    {
        return array_merge($defaultParams, $allParams);
    }

    public function arrayMergeWithAssignToAnotherVariable(array $defaultParams, array $allParams): array
    {
        $paramsList = [];
        foreach ($allParams as $oneParams) {
            $paramsList = array_merge($defaultParams, $oneParams);
        }
        return $paramsList;
    }

    public function arrayMergeDirectlyInForeach(array $defaultParams, array $otherParams): array
    {
        $params = [];
        foreach (array_merge($defaultParams, $otherParams) as $param) {
            $params[] = $param;
        }
        return $params;
    }
}

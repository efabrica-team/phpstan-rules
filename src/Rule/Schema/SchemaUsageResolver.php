<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\Schema;

use function array_key_exists;
use function is_array;
use function json_decode;
use function trim;

/**
 * Shared logic for schema rules: converts collected definitions and usages into per-parameter statistics.
 *
 * @phpstan-type Definition array{
 *     name: string,
 *     file: string,
 *     line: int,
 *     attributes: array<int, string>,
 *     hasDefault: array<int, bool>,
 *     defaults: array<int, string|null>
 * }
 * @phpstan-type Call array{0: string, 1: string, 2: int}
 * @phpstan-type ParameterStats array{used: bool, dynamic: bool, values: array<int, string>}
 */
final class SchemaUsageResolver
{
    /**
     * @param array<string, array<int, array{0: string, 1: bool, 2: string, 3: int}>> $schemaDefinitions
     *
     * @return array<string, Definition> keyed by class name without leading backslash
     */
    public function convertDefinitions(array $schemaDefinitions): array
    {
        $result = [];
        foreach ($schemaDefinitions as $file => $definitions) {
            foreach ($definitions as $definition) {
                $name = trim($definition[0], '\\');
                $converted = [
                    'name' => $name,
                    'file' => $file,
                    'line' => $definition[3],
                    'attributes' => [],
                    'hasDefault' => [],
                    'defaults' => [],
                ];
                $parameters = json_decode($definition[2], true);
                if (is_array($parameters)) {
                    foreach ($parameters as $parameter) {
                        $key = (int) $parameter['key'];
                        $converted['attributes'][$key] = (string) $parameter['name'];
                        $converted['hasDefault'][$key] = (bool) ($parameter['hasDefault'] ?? false);
                        $converted['defaults'][$key] = isset($parameter['default']) ? (string) $parameter['default'] : null;
                    }
                }
                $result[$name] = $converted;
            }
        }
        return $result;
    }

    /**
     * @param array<string, array<int, Call>> $schemaUsage
     *
     * @return array<int, Call>
     */
    public function getCalls(array $schemaUsage, string $schemaName): array
    {
        $schemaName = trim($schemaName, '\\');
        $calls = [];
        foreach ($schemaUsage as $usages) {
            foreach ($usages as $usage) {
                if (trim($usage[0], '\\') === $schemaName) {
                    $calls[] = $usage;
                }
            }
        }
        return $calls;
    }

    /**
     * For every constructor parameter computes:
     *  - used: at least one call passes a value different from the parameter default (or a non-static value),
     *  - dynamic: at least one value cannot be compared statically,
     *  - values: effective static values of all calls (argument passed, or parameter default when omitted).
     *
     * Passing the default value explicitly is treated the same as omitting the argument.
     *
     * @param Definition $definition
     * @param array<int, Call> $calls
     *
     * @return array<int, ParameterStats> keyed by parameter position
     */
    public function resolveParameters(array $definition, array $calls): array
    {
        $result = [];
        foreach ($definition['attributes'] as $key => $name) {
            $result[$key] = ['used' => false, 'dynamic' => false, 'values' => []];
        }

        foreach ($calls as $call) {
            $arguments = json_decode($call[1], true);
            if (!is_array($arguments)) {
                continue;
            }

            $passed = [];
            $unpackFrom = null;
            foreach ($arguments as $argument) {
                if (!empty($argument['unpack'])) {
                    $unpackFrom = (int) $argument['key'];
                    break;
                }
                $key = isset($argument['name']) ? $this->findKey($definition, (string) $argument['name']) : (int) $argument['key'];
                if ($key === null || !isset($result[$key])) {
                    continue;
                }
                $passed[$key] = isset($argument['value']) ? (string) $argument['value'] : null;
            }

            foreach ($result as $key => $stats) {
                if ($unpackFrom !== null && $key >= $unpackFrom) {
                    $result[$key]['used'] = true;
                    $result[$key]['dynamic'] = true;
                    continue;
                }

                if (array_key_exists($key, $passed)) {
                    $value = $passed[$key];
                    if ($value === null) {
                        $result[$key]['used'] = true;
                        $result[$key]['dynamic'] = true;
                        continue;
                    }
                    $result[$key]['values'][] = $value;
                    if (!$definition['hasDefault'][$key] || $value !== $definition['defaults'][$key]) {
                        $result[$key]['used'] = true;
                    }
                    continue;
                }

                if (!$definition['hasDefault'][$key]) {
                    continue;
                }
                $default = $definition['defaults'][$key];
                if ($default === null) {
                    $result[$key]['dynamic'] = true;
                    continue;
                }
                $result[$key]['values'][] = $default;
            }
        }

        return $result;
    }

    /**
     * @param Definition $definition
     */
    private function findKey(array $definition, string $attributeName): ?int
    {
        foreach ($definition['attributes'] as $key => $name) {
            if ($name === $attributeName) {
                return $key;
            }
        }
        return null;
    }
}

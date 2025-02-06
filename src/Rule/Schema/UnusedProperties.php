<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\Schema;

use Efabrica\PHPStanRules\Collector\Schema\SchemaDefinitions;
use Efabrica\PHPStanRules\Collector\Schema\SchemaUsage;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<CollectedDataNode>
 */
final class UnusedProperties implements Rule
{
    /**
     * @var array<string, array{
     * 0: string,
     * 1: bool,
     * 2: string,
     * 3: int,
     * attributes: array<int, string>,
     * file: string
     * }>
     */
    private array $schemaDefinitions = [];

    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node instanceof CollectedDataNode) {
            return [];
        }
        $schemaUsage = $node->get(SchemaUsage::class);
        $this->schemaDefinitions = $this->convertSchemaDefinitions($node->get(SchemaDefinitions::class));

        $uniqeSchemas = $this->getUniqueSchemas($schemaUsage);
        $warnings = [];
        foreach ($uniqeSchemas as $schemaName) {
            $schemaUse = $this->getSchemasUse($schemaUsage, $schemaName);
            $unusedProperties = $this->getUnusedProperties($schemaUse, $schemaName);
            if (!empty($unusedProperties)) {
                $warnings[] = RuleErrorBuilder::message(sprintf(
                    'Class "%s" contains properties  "%s" called with same static values.',
                    $schemaName,
                    implode(',', $unusedProperties),
                ))
                    ->file($this->schemaDefinitions[trim($schemaName, '\\')]['file'])->line($this->schemaDefinitions[trim($schemaName, '\\')][3])->build();
            }
        }

        return $warnings;
    }

    /**
     * @param array<string, array<int, array{
     *      0: string,
     *      1: bool,
     *      2: string,
     *      3: int
     *  }>> $schemaDefinitions
     *
     * @return array<string, array{
     *      0: string,
     *      1: bool,
     *      2: string,
     *      3: int,
     *      attributes: array<int, string>,
     *      file: string
     *  }>
     */
    private function convertSchemaDefinitions(array $schemaDefinitions): array
    {
        $result = [];
        foreach ($schemaDefinitions as $key => $value) {
            $tmp = $value[0];
            $attributes = json_decode($tmp[2], true);
            $tmp['attributes'] = [];
            if (is_array($attributes)) {
                foreach ($attributes as $attribute) {
                    $tmp['attributes'][$attribute['key']] = $attribute['name'];
                }
            }

            $tmp['file'] = $key;
            $result[$tmp[0]] = $tmp;
        }
        return $result;
    }

    /**
     * @param array<string, array<int, array{
     *      0: string,
     *      1: string,
     *      2: int
     *  }>> $schemaUsage
     *
     * @return array<int, string>
     */
    private function getUniqueSchemas(array $schemaUsage): array
    {
        $tmp = [];
        foreach ($schemaUsage as $schemaArray) {
            foreach ($schemaArray as $schema) {
                $tmp[$schema[0]] = $schema;
            }
        }
        return array_keys($tmp);
    }

    /**
     * @param array<string, array<int, array{
     *      0: string,
     *      1: string,
     *      2: int
     *  }>> $schemaUsage
     *
     * @return array<int, array{
     *      0: string,
     *      1: string,
     *      2: int
     *  }>
     */
    private function getSchemasUse(array $schemaUsage, string $schemaName): array
    {
        $tmp = [];
        foreach ($schemaUsage as $schemaArray) {
            foreach ($schemaArray as $schema) {
                if ($schema[0] == $schemaName) {
                    $tmp[] = $schema;
                }
            }
        }
        return $tmp;
    }

    /**
     * @param array<int, array{
     *      0: string,
     *      1: string,
     *      2: int
     *  }> $schemas
     *
     * @return array<int, string>
     */
    private function getUnusedProperties(array $schemas, string $schemaName): array
    {
        $attributesArray = [];
        $result = [];
        foreach ($schemas as $schema) {
            $attributesArray[] = json_decode($schema[1], true);
        }
        foreach ($attributesArray as $attributes) {
            if (!is_array($attributes)) {
                continue;
            }
            foreach ($attributes as $attribute) {
                if (isset($result[$attribute['key']]) && $result[$attribute['key']] === false) {
                    continue;
                }
                $result[$attribute['key']] = $this->isUnused($attribute);
            }
        }
        $return = [];
        foreach ($result as $k => $r) {
            if ($r === true) {
                $return[] = $this->schemaDefinitions[trim($schemaName, '\\')]['attributes'][$k];
            }
        }
        return $return;
    }

    /**
     * @param array{
     *      key: int,
     *      type: class-string,
     *      aditional?: int|string
     *  } $attribute
     *
     */
    private function isUnused(array $attribute): bool
    {
        if ($attribute['type'] == 'PhpParser\\Node\\Expr\\ConstFetch') {
            return true;
        }
        if (strpos($attribute['type'], 'Scalar') !== false && !isset($attribute['aditional'])) {
            return true;
        }
        if ($attribute['type'] == 'PhpParser\\Node\\Expr\\Array_' && isset($attribute['aditional']) && $attribute['aditional'] == 0) {
            return true;
        }
        return false;
    }
}

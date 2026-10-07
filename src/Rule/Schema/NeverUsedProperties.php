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
use function count;
use function implode;
use function json_decode;
use function sprintf;
use function trim;

/**
 * @implements Rule<CollectedDataNode>
 */
final class NeverUsedProperties implements Rule
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
        $schemaUsage = $node->get(SchemaUsage::class);
        $this->schemaDefinitions = $this->convertSchemaDefinitions($node->get(SchemaDefinitions::class));

        $warnings = [];
        foreach ($this->schemaDefinitions as $schemaName => $schemaData) {
            $schemaUse = $this->getSchemasUse($schemaUsage, $schemaName);
            $unusedProperties = $this->getUnusedProperties($schemaUse, $schemaName);
            if (count($unusedProperties) > 0) {
                $warnings[] = RuleErrorBuilder::message(sprintf(
                    'Class "%s" contains never used properties "%s".',
                    $schemaName,
                    implode(',', $unusedProperties),
                ))
                    ->identifier('efabrica.schemaNeverUsedProperties')
                    ->file($this->schemaDefinitions[trim($schemaName, '\\')]['file'])
                    ->line($this->schemaDefinitions[trim($schemaName, '\\')][3])
                    ->build();
            }
        }

        return $warnings;
    }

    /**
     * @param array<string, array<int, array{0: string, 1: bool, 2: string, 3: int}>> $schemaDefinitions
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
            /** @var array<int, array{key: int, name: string, type: string}>|null $attributes */
            $attributes = json_decode($tmp[2], true);
            $tmp['attributes'] = [];
            foreach ($attributes ?? [] as $attribute) {
                $tmp['attributes'][$attribute['key']] = $attribute['name'];
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
                if (trim($schema[0], '\\') === $schemaName) {
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
        if (empty($schemas)) {
            return [];
        }
        $attributesArray = [];
        $result = [];
        foreach ($schemas as $schema) {
            $attributesArray[] = json_decode($schema[1], true);
        }

        /** @var array<int, array<int, array{key: int, type: class-string, name?: string, aditional?: bool|int|string}>|null> $attributesArray */
        foreach ($attributesArray as $attributes) {
            foreach ($attributes ?? [] as $attribute) {
                if (isset($attribute['name'])) {
                    $result[$attribute['name']] = true;
                } else {
                    $result[$attribute['key']] = true;
                }
            }
        }

        $return = [];
        foreach ($this->schemaDefinitions[trim($schemaName, '\\')]['attributes'] as $k => $r) {
            if (!isset($result[$k]) && !isset($result[$r])) {
                $return[] = $r;
            }
        }
        return $return;
    }
}

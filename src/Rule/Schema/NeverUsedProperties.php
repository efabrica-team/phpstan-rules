<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\Schema;

use Cms\PHPStan\Collectors\SchemaDefinitions;
use Cms\PHPStan\Collectors\SchemaUsage;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<CollectedDataNode>
 */
final class NeverUsedProperties implements Rule
{
    private mixed $schemaDefinitions = [];

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

        $warnings = [];
        foreach ($this->schemaDefinitions as $schemaName => $schemaData) {
            $schemaUse = $this->getSchemasUse($schemaUsage, $schemaName);
            $unusedProperties = $this->getUnusedProperties($schemaUse, $schemaName);
            if ($unusedProperties) {
                $warnings[] = RuleErrorBuilder::message(sprintf(
                    'Class "%s" contains never used properties "%s".',
                    $schemaName,
                    implode(',', $unusedProperties),
                ))
                    ->file($this->schemaDefinitions[trim($schemaName, '\\')]['file'])->line($this->schemaDefinitions[trim($schemaName, '\\')][3])->build();
            }
        }

        return $warnings;
    }

    private function convertSchemaDefinitions(mixed $schemaDefinitions): mixed
    {
        $result = [];
        foreach ($schemaDefinitions as $key => $value) {
            $tmp = $value[0];
            $attributes = json_decode($tmp[2], true);
            foreach ($attributes as $attribute) {
                $tmp['attributes'][$attribute['key']] = $attribute['name'];
            }
            $tmp['file'] = $key;
            $result[$tmp[0]] = $tmp;
        }
        return $result;
    }

    private function getSchemasUse(mixed $schemaUsage, string $schemaName): mixed
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

    private function getUnusedProperties(mixed $schemas, string $schemaName): mixed
    {
        if (empty($schemas)) {
            return [];
        }
        $attributesArray = [];
        $result = [];
        foreach ($schemas as $schema) {
            $attributesArray[] = json_decode($schema[1], true);
        }
        foreach ($attributesArray as $attributes) {
            foreach ($attributes as $attribute) {
                $result[$attribute['key']] = true;
            }
        }
        $return = [];
        foreach ($this->schemaDefinitions[trim($schemaName, '\\')]['attributes'] as $k => $r) {
            if (!isset($result[$k])) {
                $return[] = $r;
            }
        }
        return $return;
    }
}

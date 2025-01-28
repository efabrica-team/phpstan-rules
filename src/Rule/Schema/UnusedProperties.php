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
            if ($unusedProperties) {
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

    private function getUniqueSchemas(mixed $schemaUsage): mixed
    {
        $tmp = [];
        foreach ($schemaUsage as $schemaArray) {
            foreach ($schemaArray as $schema) {
                $tmp[$schema[0]] = $schema;
            }
        }
        return array_keys($tmp);
    }

    private function getSchemasUse(mixed $schemaUsage, string $schemaName): mixed
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

    private function getUnusedProperties(mixed $schemas, string $schemaName): mixed
    {
        $attributesArray = [];
        $result = [];
        foreach ($schemas as $schema) {
            $attributesArray[] = json_decode($schema[1], true);
        }
        foreach ($attributesArray as $attributes) {
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

    private function isUnused(mixed $attribute): bool
    {
        if ($attribute['type'] == 'PhpParser\\Node\\Expr\\ConstFetch') {
            return true;
        }
        if (strpos($attribute['type'], 'Scalar') !== false && !$attribute['aditional']) {
            return true;
        }
        if ($attribute['type'] == 'PhpParser\\Node\\Expr\\Array_' && $attribute['aditional'] == 0) {
            return true;
        }
        return false;
    }
}

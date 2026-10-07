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
use function sprintf;

/**
 * @implements Rule<CollectedDataNode>
 */
final class NeverUsedProperties implements Rule
{
    private SchemaUsageResolver $resolver;

    public function __construct()
    {
        $this->resolver = new SchemaUsageResolver();
    }

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
        $schemaDefinitions = $this->resolver->convertDefinitions($node->get(SchemaDefinitions::class));

        $warnings = [];
        foreach ($schemaDefinitions as $schemaName => $definition) {
            $calls = $this->resolver->getCalls($schemaUsage, $schemaName);
            if (count($calls) === 0) {
                continue;
            }

            $unusedProperties = [];
            foreach ($this->resolver->resolveParameters($definition, $calls) as $key => $stats) {
                if (!$stats['used']) {
                    $unusedProperties[] = $definition['attributes'][$key];
                }
            }

            if (count($unusedProperties) > 0) {
                $warnings[] = RuleErrorBuilder::message(sprintf(
                    'Class "%s" contains never used properties "%s".',
                    $schemaName,
                    implode(',', $unusedProperties),
                ))
                    ->file($definition['file'])->line($definition['line'])->build();
            }
        }

        return $warnings;
    }
}

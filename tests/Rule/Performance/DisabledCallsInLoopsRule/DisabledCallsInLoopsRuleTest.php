<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Performance\DisabledCallsInLoopsRule;

use Efabrica\PHPStanRules\Rule\Performance\DisabledCallsInLoopsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class DisabledCallsInLoopsRuleTest extends RuleTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../../../extension.neon',
            __DIR__ . '/../../../../rules.neon',
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getContainer()->getByType(DisabledCallsInLoopsRule::class);
    }

    public function testArrayMerge(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/ArrayMerge.php'], [
            [
                'Performance: Do not use "array_merge" in loop.',
                13,
            ],
            [
                'Performance: Do not use "array_merge" in loop.',
                22,
            ],
            [
                'Performance: Do not use "array_merge" in loop.',
                32,
            ],
            [
                'Performance: Do not use "array_merge" in loop.',
                43,
            ],
        ]);
    }

    public function testArrayMergeRecursive(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/ArrayMergeRecursive.php'], [
            [
                'Performance: Do not use "array_merge_recursive" in loop.',
                13,
            ],
            [
                'Performance: Do not use "array_merge_recursive" in loop.',
                22,
            ],
            [
                'Performance: Do not use "array_merge_recursive" in loop.',
                32,
            ],
            [
                'Performance: Do not use "array_merge_recursive" in loop.',
                43,
            ],
        ]);
    }
}
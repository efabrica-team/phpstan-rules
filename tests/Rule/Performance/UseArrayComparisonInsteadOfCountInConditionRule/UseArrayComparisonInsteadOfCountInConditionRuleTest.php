<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Performance\UseArrayComparisonInsteadOfCountInConditionRule;

use Efabrica\PHPStanRules\Rule\Performance\UseArrayComparisonInsteadOfCountInConditionRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class UseArrayComparisonInsteadOfCountInConditionRuleTest extends RuleTestCase
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
        return $this->getContainer()->getByType(UseArrayComparisonInsteadOfCountInConditionRule::class);
    }

    public function testWrong(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/Wrong.php'], [
            [
                'Use "$array !== []" instead of count() when checking for non-empty array in condition.',
                22,
            ],
            [
                'Use "$array === []" instead of count() when checking for empty array in condition.',
                31,
            ],
            [
                'Use "$array !== []" instead of count() when checking for non-empty array in condition.',
                40,
            ],
            [
                'Use "$array !== []" instead of count() when checking for non-empty array in condition.',
                49,
            ],
            [
                'Use "$array !== []" instead of count() when checking for non-empty array in condition.',
                58,
            ],
            [
                'Use "$array !== []" instead of count() when checking for non-empty array in condition.',
                67,
            ],
            [
                'Use "$array === []" instead of count() when checking for empty array in condition.',
                76,
            ],
            [
                'Use "$array === []" instead of count() when checking for empty array in condition.',
                85,
            ],
            [
                'Use "$array === []" instead of count() when checking for empty array in condition.',
                94,
            ],
            [
                'Use "$array === []" instead of count() when checking for empty array in condition.',
                103,
            ],
            [
                'Use "$array !== []" instead of count() when checking for non-empty array in condition.',
                112,
            ],
            [
                'Use "$array === []" instead of count() when checking for empty array in condition.',
                121,
            ],
            [
                'Use "$array !== []" instead of count() when checking for non-empty array in condition.',
                132,
            ],
        ]);
    }

    public function testCorrect(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/Correct.php'], []);
    }
}

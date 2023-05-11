<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Performance\CheckSlowCallsInConditionsRule;

use Efabrica\PHPStanRules\Rule\Performance\CheckSlowCallsInConditionsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class CheckSlowCallsInConditionsRuleTest extends RuleTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../../../extension.neon',
            __DIR__ . '/rules.neon',
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getContainer()->getByType(CheckSlowCallsInConditionsRule::class);
    }

    public function testNoSlowCallsInConditions(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/NoSlowCallsInConditions.php'], []);
    }

    public function testSlowCallsInConditions(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/SlowCallsInConditions.php'], [
            [
                'Performance: Slow call "file_exists()" is called in condition before faster expressions. Move it to the end.',
                21,
            ],
            [
                'Performance: Slow call "file_exists()" is called in condition before faster expressions. Move it to the end.',
                29,
            ],
            [
                'Performance: Slow call "file_exists()" is called in condition before faster expressions. Move it to the end.',
                37,
            ],
            [
                'Performance: Slow call "file_exists()" is called in condition before faster expressions. Move it to the end.',
                45,
            ],
            [
                'Performance: Slow call "file_exists()" is called in condition before faster expressions. Move it to the end.',
                69,
            ],
        ]);
    }
}

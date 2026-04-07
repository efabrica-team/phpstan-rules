<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\EnforceArrowFunctionRule;

use Efabrica\PHPStanRules\Rule\General\EnforceArrowFunctionRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class EnforceArrowFunctionRuleTest extends RuleTestCase
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
        return $this->getContainer()->getByType(EnforceArrowFunctionRule::class);
    }

    public function testWrong(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/Wrong.php'], [
            [
                'Closure has a single return expression. Use an arrow function instead.',
                15,
            ],
            [
                'Closure has a single return expression. Use an arrow function instead.',
                26,
            ],
        ]);
    }

    public function testCorrect(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/Correct.php'], []);
    }
}

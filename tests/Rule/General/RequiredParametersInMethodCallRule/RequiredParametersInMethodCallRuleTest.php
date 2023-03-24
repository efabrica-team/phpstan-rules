<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\RequiredParametersInMethodCallRule;

use Efabrica\PHPStanRules\Rule\General\RequiredParametersInMethodCallRule;
use Efabrica\PHPStanRules\Tests\Rule\General\RequiredParametersInMethodCallRule\Source\Selection;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class RequiredParametersInMethodCallRuleTest extends RuleTestCase
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
        return $this->getContainer()->getByType(RequiredParametersInMethodCallRule::class);
    }

    public function testSelection(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/CallSelectionCount.php'], [
            [
                'Parameter \'column\' of method ' . Selection::class . '::count() is required to be string, none given.',
                19,
                'Always use parameter column as string, because...'
            ],
            [
                'Parameter \'column\' of method ' . Selection::class . '::count() is required to be string, null given.',
                20,
                'Always use parameter column as string, because...'
            ],
        ]);
    }
}

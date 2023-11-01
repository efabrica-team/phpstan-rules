<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Performance\UseNetteDatabaseSelectionFetchTogetherWithLimitRule;

use Efabrica\PHPStanRules\Rule\Performance\UseNetteDatabaseSelectionFetchTogetherWithLimitRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class UseNetteDatabaseSelectionFetchTogetherWithLimitRuleTest extends RuleTestCase
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
        return $this->getContainer()->getByType(UseNetteDatabaseSelectionFetchTogetherWithLimitRule::class);
    }

    public function test(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/NetteDatabaseSelection.php'], [
            [
                'Use Nette\Database\Selection::fetch() in combination with limit(1)',
                14,
            ],
            [
                'Use Nette\Database\Selection::fetch() in combination with limit(1)',
                19,
            ],
            [
                'Use Nette\Database\Selection::fetch() in combination with limit(1)',
                29,
            ],
            [
                'Use Nette\Database\Selection::fetch() in combination with limit(1)',
                52,
            ],
        ]);
    }
}

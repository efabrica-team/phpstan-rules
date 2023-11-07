<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\DisabledConcatenationWithTranslatedStringsRule;

use Efabrica\PHPStanRules\Rule\General\DisabledConcatenationWithTranslatedStringsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class DisabledConcatenationWithTranslatedStringsRuleTest extends RuleTestCase
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
        return $this->getContainer()->getByType(DisabledConcatenationWithTranslatedStringsRule::class);
    }

    public function testConcat(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/Concat.php'], [
            [
                'Do not concatenate translated strings.',
                14,
                'Every language has its own word ordering, use variables in translations instead, e.g. Hello %name%.',
            ],
            [
                'Do not concatenate translated strings.',
                15,
                'Every language has its own word ordering, use variables in translations instead, e.g. Hello %name%.',
            ],
            [
                'Do not concatenate translated strings.',
                16,
                'Every language has its own word ordering, use variables in translations instead, e.g. Hello %name%.',
            ],
            [
                'Do not concatenate translated strings.',
                21,
                'Every language has its own word ordering, use variables in translations instead, e.g. Hello %name%.',
            ],
            [
                'Do not concatenate translated strings.',
                22,
                'Every language has its own word ordering, use variables in translations instead, e.g. Hello %name%.',
            ],
            [
                'Do not concatenate translated strings.',
                23,
                'Every language has its own word ordering, use variables in translations instead, e.g. Hello %name%.',
            ],
            [
                'Do not concatenate translated strings.',
                28,
                'Every language has its own word ordering, use variables in translations instead, e.g. Hello %name%.',
            ],
            [
                'Do not concatenate translated strings.',
                29,
                'Every language has its own word ordering, use variables in translations instead, e.g. Hello %name%.',
            ],
            [
                'Do not concatenate translated strings.',
                30,
                'Every language has its own word ordering, use variables in translations instead, e.g. Hello %name%.',
            ],
            [
                'Do not concatenate translated strings.',
                35,
                'Every language has its own word ordering, use variables in translations instead, e.g. Hello %name%.',
            ],
            [
                'Do not concatenate translated strings.',
                36,
                'Every language has its own word ordering, use variables in translations instead, e.g. Hello %name%.',
            ],
            [
                'Do not concatenate translated strings.',
                37,
                'Every language has its own word ordering, use variables in translations instead, e.g. Hello %name%.',
            ],
            [
                'Do not concatenate translated strings.',
                38,
                'Every language has its own word ordering, use variables in translations instead, e.g. Hello %name%.',
            ],
            [
                'Do not concatenate translated strings.',
                38,
                'Every language has its own word ordering, use variables in translations instead, e.g. Hello %name%.',
            ],
        ]);
    }

    public function testCorrectCalls(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/Correct.php'], []);
    }
}

<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule;

use Efabrica\PHPStanRules\Rule\General\TraitContextRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class TraitContextRuleTest extends RuleTestCase
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
        return $this->getContainer()->getByType(TraitContextRule::class);
    }

    public function testNoTimeoutWithClientAsPrivateProperty(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/AllTraitsCorrect.php'], []);
        $this->analyse([__DIR__ . '/Fixtures/OneTraitCorrectAndOneWrong.php'], [
            [
                'Trait Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Source\SecondTrait is used in wrong context.',
                14,
            ],
        ]);
        $this->analyse([__DIR__ . '/Fixtures/AllTraitsWrong.php'], [
            [
                'Trait Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Source\FirstTrait is used in wrong context.',
                13,
            ],
            [
                'Trait Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Source\SecondTrait is used in wrong context.',
                14,
            ],
            [
                'Trait Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Source\ThirdTrait is used in wrong context.',
                15,
            ],
        ]);
    }
}

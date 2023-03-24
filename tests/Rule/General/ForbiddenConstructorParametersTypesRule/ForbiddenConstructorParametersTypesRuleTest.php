<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\ForbiddenConstructorParametersTypesRule;

use Efabrica\PHPStanRules\Rule\General\ForbiddenConstructorParametersTypesRule;
use Efabrica\PHPStanRules\Tests\Rule\General\ForbiddenConstructorParametersTypesRule\Fixtures\UsingForbidden;
use Efabrica\PHPStanRules\Tests\Rule\General\ForbiddenConstructorParametersTypesRule\Fixtures\UsingForbiddenAndForbiddenChild;
use Efabrica\PHPStanRules\Tests\Rule\General\ForbiddenConstructorParametersTypesRule\Fixtures\UsingMultipleForbidden;
use Efabrica\PHPStanRules\Tests\Rule\General\ForbiddenConstructorParametersTypesRule\Source\ForbiddenChildType;
use Efabrica\PHPStanRules\Tests\Rule\General\ForbiddenConstructorParametersTypesRule\Source\ForbiddenType;
use Efabrica\PHPStanRules\Tests\Rule\General\ForbiddenConstructorParametersTypesRule\Source\NotForbiddenType;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class ForbiddenConstructorParametersTypesRuleTest extends RuleTestCase
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
        return $this->getContainer()->getByType(ForbiddenConstructorParametersTypesRule::class);
    }

    public function testUsingForbidden(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/UsingForbidden.php'], [
            [
                'Constructor parameter #2 of class ' . UsingForbidden::class . ' has forbidden type ' . ForbiddenType::class . '.',
                16,
                'Use ' . NotForbiddenType::class . ' instead.',
            ],
        ]);
    }

    public function testUsingForbiddenAndForbiddenChild(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/UsingForbiddenAndForbiddenChild.php'], [
            [
                'Constructor parameter #2 of class ' . UsingForbiddenAndForbiddenChild::class . ' has forbidden type ' . ForbiddenType::class . '.',
                19,
                'Use ' . NotForbiddenType::class . ' instead.',
            ],
            [
                'Constructor parameter #3 of class ' . UsingForbiddenAndForbiddenChild::class . ' has forbidden type ' . ForbiddenChildType::class . '.',
                19,
                'Use ' . NotForbiddenType::class . ' instead.',
            ],
        ]);
    }

    public function testUsingMultipleForbidden(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/UsingMultipleForbidden.php'], [
            [
                'Constructor parameter #2 of class ' . UsingMultipleForbidden::class . ' has forbidden type ' . ForbiddenType::class . '.',
                18,
                'Use ' . NotForbiddenType::class . ' instead.',
            ],
            [
                'Constructor parameter #3 of class ' . UsingMultipleForbidden::class . ' has forbidden type ' . ForbiddenType::class . '.',
                18,
                'Use ' . NotForbiddenType::class . ' instead.',
            ],
        ]);
    }

    public function testUsingNotForbidden(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/UsingNotForbidden.php'], []);
    }

    public function testUsingForbiddenInDifferentContext(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/UsingForbiddenInDifferentContext.php'], []);
    }
}

<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\DisableMethodCallInContextRule;

use Efabrica\PHPStanRules\Rule\General\DisableMethodCallInContextRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class DisableMethodCallInContextRuleTest extends RuleTestCase
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
        return $this->getContainer()->getByType(DisableMethodCallInContextRule::class);
    }

    public function testFindDisabledMethod(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/ClassWithCallDisabledMethod.php'], [
            [
                'Calling method Efabrica\PHPStanRules\Tests\Rule\General\DisableMethodCallInContextRule\Source\CheckedClass::disabledMethod() in Efabrica\PHPStanRules\Tests\Rule\General\DisableMethodCallInContextRule\Fixtures\ClassWithCallDisabledMethod::checkedMethod() is forbidden.',
                24,
            ],
        ]);
    }

    public function testCorrectCalls(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/ClassWithoutDisablingCallDisabledMethod.php'], []);
    }
}

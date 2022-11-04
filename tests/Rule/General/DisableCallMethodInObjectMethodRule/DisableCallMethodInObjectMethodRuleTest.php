<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Notcom\DisableCallMethodInObjectMethodRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class DisableCallMethodInObjectMethodRuleTest extends RuleTestCase
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
        return $this->getContainer()->getService('disableCallMethodInObjectMethodRule');
    }

    public function testFindDisabledMethod(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/ClassWithCallDisabledMethod.php'], [
            [
                'Method Efabrica\PHPStanRules\Tests\Rule\General\DisableCallMethodInObjectMethodRule\Fixtures\ClassWithCallDisabledMethod::checkedMethod() called Efabrica\PHPStanRules\Tests\Rule\General\DisableCallMethodInObjectMethodRule\Source\CheckedClass::disabledMethod().',
                24,
            ],
        ]);
    }

    public function testCorrectCalls(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/ClassWithoutDisablingCallDisabledMethod.php'], []);
    }
}

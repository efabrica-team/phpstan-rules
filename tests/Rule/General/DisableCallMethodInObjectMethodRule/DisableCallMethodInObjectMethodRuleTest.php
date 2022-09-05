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
            __DIR__ . '/../../../../rules.neon',
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getContainer()->getService('disableCallMethodInObjectMethodRule');
    }

    public function testFindSetRequired(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/PluginWithSetRequiredMin.php'], [
            [
                'Method App\PluginWithSetRequiredMin::pageConfiguration is called with setRequired() option.',
                10,
            ],
        ]);
        $this->analyse([__DIR__ . '/Fixtures/PluginWithSetRequired.php'], [
            [
                'Method BaseModule\Plugin\Header\PluginWithSetRequired::pageConfiguration is called with setRequired() option.',
                19,
            ],
        ]);
    }

    public function testCorrectCalls(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/PluginWithoutSetRequired.php'], []);
    }
}

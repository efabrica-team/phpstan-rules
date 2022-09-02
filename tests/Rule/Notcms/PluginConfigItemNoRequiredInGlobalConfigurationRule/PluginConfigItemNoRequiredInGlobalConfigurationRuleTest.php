<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Notcom\PluginConfigItemNoRequiredInGlobalConfigurationRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class PluginConfigItemNoRequiredInGlobalConfigurationRuleTest extends RuleTestCase
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
        return $this->getContainer()->getService('pluginConfigItemNoRequiredInGlobalConfigurationRule');
    }

    public function testFindSetRequired(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/PluginWithSetRequiredMin.php'], [
            [
                'Method App\PluginWithSetRequiredMin::globalConfiguration is called with setRequired() option.',
                8,
            ],
        ]);
        $this->analyse([__DIR__ . '/Fixtures/PluginWithSetRequired.php'], [
            [
                'Method BaseModule\Plugin\Header\PluginWithSetRequired::globalConfiguration is called with setRequired() option.',
                31,
            ],
        ]);
    }

    public function testCorrectCalls(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/PluginWithoutSetRequired.php'], []);
    }
}

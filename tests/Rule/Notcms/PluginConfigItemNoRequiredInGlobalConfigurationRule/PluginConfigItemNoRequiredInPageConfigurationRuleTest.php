<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Notcom\PluginConfigItemNoRequiredInGlobalConfigurationRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class PluginConfigItemNoRequiredInPageConfigurationRuleTest extends RuleTestCase
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
        return $this->getContainer()->getService('pluginConfigItemNoRequiredInPageConfigurationRule');
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
                22,
            ],
        ]);
    }

    public function testCorrectCalls(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/PluginWithoutSetRequired.php'], []);
        $this->analyse([__DIR__ . '/Fixtures/EmptyClass.php'], []);
        $this->analyse([__DIR__ . '/Fixtures/PluginWithEmptyPageConfiguration.php'], []);
        $this->analyse([__DIR__ . '/Fixtures/RandomClassWithSetRequired.php'], []);
    }
}

<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Guzzle\ClientCallWithoutOptionRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

abstract class ClientCallWithoutOptionRuleTest extends RuleTestCase
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
        return $this->getContainer()->getService($this->getServiceName());
    }

    abstract protected function getServiceName(): string;

    abstract protected function getOptionName(): string;

    public function testCallsWithNoOptions(): void
    {
        $optionName = $this->getOptionName();

        $this->analyse([__DIR__ . '/Fixtures/CallsWithNoOptions.php'], [
            [
                'Method GuzzleHttp\Client::get is called without ' . $optionName . ' option.',
                21,
            ],
            [
                'Method GuzzleHttp\Client::post is called without ' . $optionName . ' option.',
                22,
            ],
            [
                'Method GuzzleHttp\Client::put is called without ' . $optionName . ' option.',
                23,
            ],
            [
                'Method GuzzleHttp\Client::head is called without ' . $optionName . ' option.',
                24,
            ],
            [
                'Method GuzzleHttp\Client::patch is called without ' . $optionName . ' option.',
                25,
            ],
            [
                'Method GuzzleHttp\Client::delete is called without ' . $optionName . ' option.',
                26,
            ],
            [
                'Method GuzzleHttp\Client::send is called without ' . $optionName . ' option.',
                27,
            ],
            [
                'Method GuzzleHttp\Client::request is called without ' . $optionName . ' option.',
                28,
            ],
            [
                'Method GuzzleHttp\Client::getAsync is called without ' . $optionName . ' option.',
                30,
            ],
            [
                'Method GuzzleHttp\Client::postAsync is called without ' . $optionName . ' option.',
                31,
            ],
            [
                'Method GuzzleHttp\Client::putAsync is called without ' . $optionName . ' option.',
                32,
            ],
            [
                'Method GuzzleHttp\Client::headAsync is called without ' . $optionName . ' option.',
                33,
            ],
            [
                'Method GuzzleHttp\Client::patchAsync is called without ' . $optionName . ' option.',
                34,
            ],
            [
                'Method GuzzleHttp\Client::deleteAsync is called without ' . $optionName . ' option.',
                35,
            ],
            [
                'Method GuzzleHttp\Client::sendAsync is called without ' . $optionName . ' option.',
                36,
            ],
            [
                'Method GuzzleHttp\Client::requestAsync is called without ' . $optionName . ' option.',
                37,
            ],
            [
                'Method GuzzleHttp\Client::get is called without ' . $optionName . ' option.',
                45,
            ],
            [
                'Method GuzzleHttp\Client::post is called without ' . $optionName . ' option.',
                46,
            ],
            [
                'Method GuzzleHttp\Client::put is called without ' . $optionName . ' option.',
                47,
            ],
            [
                'Method GuzzleHttp\Client::head is called without ' . $optionName . ' option.',
                48,
            ],
            [
                'Method GuzzleHttp\Client::patch is called without ' . $optionName . ' option.',
                49,
            ],
            [
                'Method GuzzleHttp\Client::delete is called without ' . $optionName . ' option.',
                50,
            ],
            [
                'Method GuzzleHttp\Client::send is called without ' . $optionName . ' option.',
                51,
            ],
            [
                'Method GuzzleHttp\Client::request is called without ' . $optionName . ' option.',
                52,
            ],
            [
                'Method GuzzleHttp\Client::getAsync is called without ' . $optionName . ' option.',
                54,
            ],
            [
                'Method GuzzleHttp\Client::postAsync is called without ' . $optionName . ' option.',
                55,
            ],
            [
                'Method GuzzleHttp\Client::putAsync is called without ' . $optionName . ' option.',
                56,
            ],
            [
                'Method GuzzleHttp\Client::headAsync is called without ' . $optionName . ' option.',
                57,
            ],
            [
                'Method GuzzleHttp\Client::patchAsync is called without ' . $optionName . ' option.',
                58,
            ],
            [
                'Method GuzzleHttp\Client::deleteAsync is called without ' . $optionName . ' option.',
                59,
            ],
            [
                'Method GuzzleHttp\Client::sendAsync is called without ' . $optionName . ' option.',
                60,
            ],
            [
                'Method GuzzleHttp\Client::requestAsync is called without ' . $optionName . ' option.',
                61,
            ],
        ]);
    }

    public function testCallsWithEmptyOptions(): void
    {
        $optionName = $this->getOptionName();

        $this->analyse([__DIR__ . '/Fixtures/CallsWithEmptyArray.php'], [
            [
                'Method GuzzleHttp\Client::get is called without ' . $optionName . ' option.',
                21,
            ],
            [
                'Method GuzzleHttp\Client::post is called without ' . $optionName . ' option.',
                22,
            ],
            [
                'Method GuzzleHttp\Client::put is called without ' . $optionName . ' option.',
                23,
            ],
            [
                'Method GuzzleHttp\Client::head is called without ' . $optionName . ' option.',
                24,
            ],
            [
                'Method GuzzleHttp\Client::patch is called without ' . $optionName . ' option.',
                25,
            ],
            [
                'Method GuzzleHttp\Client::delete is called without ' . $optionName . ' option.',
                26,
            ],
            [
                'Method GuzzleHttp\Client::send is called without ' . $optionName . ' option.',
                27,
            ],
            [
                'Method GuzzleHttp\Client::request is called without ' . $optionName . ' option.',
                28,
            ],
            [
                'Method GuzzleHttp\Client::getAsync is called without ' . $optionName . ' option.',
                31,
            ],
            [
                'Method GuzzleHttp\Client::postAsync is called without ' . $optionName . ' option.',
                32,
            ],
            [
                'Method GuzzleHttp\Client::putAsync is called without ' . $optionName . ' option.',
                33,
            ],
            [
                'Method GuzzleHttp\Client::headAsync is called without ' . $optionName . ' option.',
                34,
            ],
            [
                'Method GuzzleHttp\Client::patchAsync is called without ' . $optionName . ' option.',
                35,
            ],
            [
                'Method GuzzleHttp\Client::deleteAsync is called without ' . $optionName . ' option.',
                36,
            ],
            [
                'Method GuzzleHttp\Client::sendAsync is called without ' . $optionName . ' option.',
                37,
            ],
            [
                'Method GuzzleHttp\Client::requestAsync is called without ' . $optionName . ' option.',
                38,
            ],
        ]);
    }

    public function testCorrectCalls(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/CorrectCalls.php'], []);
    }
}

<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Guzzle\ClientCallWithoutOptionRule;

use Efabrica\PHPStanRules\Rule\Guzzle\ClientCallWithoutOptionRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class ClientCallWithoutOptionRuleTest extends RuleTestCase
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
        return $this->getContainer()->getByType(ClientCallWithoutOptionRule::class);
    }

    public function testCallsWithNoOptions(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/CallsWithNoOptions.php'], [
            [
                'Method GuzzleHttp\Client::get is called without timeout option.',
                21,
            ],
            [
                'Method GuzzleHttp\Client::get is called without connect_timeout option.',
                21,
            ],
            [
                'Method GuzzleHttp\Client::post is called without timeout option.',
                22,
            ],
            [
                'Method GuzzleHttp\Client::post is called without connect_timeout option.',
                22,
            ],
            [
                'Method GuzzleHttp\Client::put is called without timeout option.',
                23,
            ],
            [
                'Method GuzzleHttp\Client::put is called without connect_timeout option.',
                23,
            ],
            [
                'Method GuzzleHttp\Client::head is called without timeout option.',
                24,
            ],
            [
                'Method GuzzleHttp\Client::head is called without connect_timeout option.',
                24,
            ],
            [
                'Method GuzzleHttp\Client::patch is called without timeout option.',
                25,
            ],
            [
                'Method GuzzleHttp\Client::patch is called without connect_timeout option.',
                25,
            ],
            [
                'Method GuzzleHttp\Client::delete is called without timeout option.',
                26,
            ],
            [
                'Method GuzzleHttp\Client::delete is called without connect_timeout option.',
                26,
            ],
            [
                'Method GuzzleHttp\Client::send is called without timeout option.',
                27,
            ],
            [
                'Method GuzzleHttp\Client::send is called without connect_timeout option.',
                27,
            ],
            [
                'Method GuzzleHttp\Client::request is called without timeout option.',
                28,
            ],
            [
                'Method GuzzleHttp\Client::request is called without connect_timeout option.',
                28,
            ],
            [
                'Method GuzzleHttp\Client::getAsync is called without timeout option.',
                30,
            ],
            [
                'Method GuzzleHttp\Client::getAsync is called without connect_timeout option.',
                30,
            ],
            [
                'Method GuzzleHttp\Client::postAsync is called without timeout option.',
                31,
            ],
            [
                'Method GuzzleHttp\Client::postAsync is called without connect_timeout option.',
                31,
            ],
            [
                'Method GuzzleHttp\Client::putAsync is called without timeout option.',
                32,
            ],
            [
                'Method GuzzleHttp\Client::putAsync is called without connect_timeout option.',
                32,
            ],
            [
                'Method GuzzleHttp\Client::headAsync is called without timeout option.',
                33,
            ],
            [
                'Method GuzzleHttp\Client::headAsync is called without connect_timeout option.',
                33,
            ],
            [
                'Method GuzzleHttp\Client::patchAsync is called without timeout option.',
                34,
            ],
            [
                'Method GuzzleHttp\Client::patchAsync is called without connect_timeout option.',
                34,
            ],
            [
                'Method GuzzleHttp\Client::deleteAsync is called without timeout option.',
                35,
            ],
            [
                'Method GuzzleHttp\Client::deleteAsync is called without connect_timeout option.',
                35,
            ],
            [
                'Method GuzzleHttp\Client::sendAsync is called without timeout option.',
                36,
            ],
            [
                'Method GuzzleHttp\Client::sendAsync is called without connect_timeout option.',
                36,
            ],
            [
                'Method GuzzleHttp\Client::requestAsync is called without timeout option.',
                37,
            ],
            [
                'Method GuzzleHttp\Client::requestAsync is called without connect_timeout option.',
                37,
            ],
            [
                'Method GuzzleHttp\Client::get is called without timeout option.',
                45,
            ],
            [
                'Method GuzzleHttp\Client::get is called without connect_timeout option.',
                45,
            ],
            [
                'Method GuzzleHttp\Client::post is called without timeout option.',
                46,
            ],
            [
                'Method GuzzleHttp\Client::post is called without connect_timeout option.',
                46,
            ],
            [
                'Method GuzzleHttp\Client::put is called without timeout option.',
                47,
            ],
            [
                'Method GuzzleHttp\Client::put is called without connect_timeout option.',
                47,
            ],
            [
                'Method GuzzleHttp\Client::head is called without timeout option.',
                48,
            ],
            [
                'Method GuzzleHttp\Client::head is called without connect_timeout option.',
                48,
            ],
            [
                'Method GuzzleHttp\Client::patch is called without timeout option.',
                49,
            ],
            [
                'Method GuzzleHttp\Client::patch is called without connect_timeout option.',
                49,
            ],
            [
                'Method GuzzleHttp\Client::delete is called without timeout option.',
                50,
            ],
            [
                'Method GuzzleHttp\Client::delete is called without connect_timeout option.',
                50,
            ],
            [
                'Method GuzzleHttp\Client::send is called without timeout option.',
                51,
            ],
            [
                'Method GuzzleHttp\Client::send is called without connect_timeout option.',
                51,
            ],
            [
                'Method GuzzleHttp\Client::request is called without timeout option.',
                52,
            ],
            [
                'Method GuzzleHttp\Client::request is called without connect_timeout option.',
                52,
            ],
            [
                'Method GuzzleHttp\Client::getAsync is called without timeout option.',
                54,
            ],
            [
                'Method GuzzleHttp\Client::getAsync is called without connect_timeout option.',
                54,
            ],
            [
                'Method GuzzleHttp\Client::postAsync is called without timeout option.',
                55,
            ],
            [
                'Method GuzzleHttp\Client::postAsync is called without connect_timeout option.',
                55,
            ],
            [
                'Method GuzzleHttp\Client::putAsync is called without timeout option.',
                56,
            ],
            [
                'Method GuzzleHttp\Client::putAsync is called without connect_timeout option.',
                56,
            ],
            [
                'Method GuzzleHttp\Client::headAsync is called without timeout option.',
                57,
            ],
            [
                'Method GuzzleHttp\Client::headAsync is called without connect_timeout option.',
                57,
            ],
            [
                'Method GuzzleHttp\Client::patchAsync is called without timeout option.',
                58,
            ],
            [
                'Method GuzzleHttp\Client::patchAsync is called without connect_timeout option.',
                58,
            ],
            [
                'Method GuzzleHttp\Client::deleteAsync is called without timeout option.',
                59,
            ],
            [
                'Method GuzzleHttp\Client::deleteAsync is called without connect_timeout option.',
                59,
            ],
            [
                'Method GuzzleHttp\Client::sendAsync is called without timeout option.',
                60,
            ],
            [
                'Method GuzzleHttp\Client::sendAsync is called without connect_timeout option.',
                60,
            ],
            [
                'Method GuzzleHttp\Client::requestAsync is called without timeout option.',
                61,
            ],
            [
                'Method GuzzleHttp\Client::requestAsync is called without connect_timeout option.',
                61,
            ],
        ]);
    }

    public function testCallsWithEmptyOptions(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/CallsWithEmptyArray.php'], [
            [
                'Method GuzzleHttp\Client::get is called without timeout option.',
                21,
            ],
            [
                'Method GuzzleHttp\Client::get is called without connect_timeout option.',
                21,
            ],
            [
                'Method GuzzleHttp\Client::post is called without timeout option.',
                22,
            ],
            [
                'Method GuzzleHttp\Client::post is called without connect_timeout option.',
                22,
            ],
            [
                'Method GuzzleHttp\Client::put is called without timeout option.',
                23,
            ],
            [
                'Method GuzzleHttp\Client::put is called without connect_timeout option.',
                23,
            ],
            [
                'Method GuzzleHttp\Client::head is called without timeout option.',
                24,
            ],
            [
                'Method GuzzleHttp\Client::head is called without connect_timeout option.',
                24,
            ],
            [
                'Method GuzzleHttp\Client::patch is called without timeout option.',
                25,
            ],
            [
                'Method GuzzleHttp\Client::patch is called without connect_timeout option.',
                25,
            ],
            [
                'Method GuzzleHttp\Client::delete is called without timeout option.',
                26,
            ],
            [
                'Method GuzzleHttp\Client::delete is called without connect_timeout option.',
                26,
            ],
            [
                'Method GuzzleHttp\Client::send is called without timeout option.',
                27,
            ],
            [
                'Method GuzzleHttp\Client::send is called without connect_timeout option.',
                27,
            ],
            [
                'Method GuzzleHttp\Client::request is called without timeout option.',
                28,
            ],
            [
                'Method GuzzleHttp\Client::request is called without connect_timeout option.',
                28,
            ],
            [
                'Method GuzzleHttp\Client::getAsync is called without timeout option.',
                31,
            ],
            [
                'Method GuzzleHttp\Client::getAsync is called without connect_timeout option.',
                31,
            ],
            [
                'Method GuzzleHttp\Client::postAsync is called without timeout option.',
                32,
            ],
            [
                'Method GuzzleHttp\Client::postAsync is called without connect_timeout option.',
                32,
            ],
            [
                'Method GuzzleHttp\Client::putAsync is called without timeout option.',
                33,
            ],
            [
                'Method GuzzleHttp\Client::putAsync is called without connect_timeout option.',
                33,
            ],
            [
                'Method GuzzleHttp\Client::headAsync is called without timeout option.',
                34,
            ],
            [
                'Method GuzzleHttp\Client::headAsync is called without connect_timeout option.',
                34,
            ],
            [
                'Method GuzzleHttp\Client::patchAsync is called without timeout option.',
                35,
            ],
            [
                'Method GuzzleHttp\Client::patchAsync is called without connect_timeout option.',
                35,
            ],
            [
                'Method GuzzleHttp\Client::deleteAsync is called without timeout option.',
                36,
            ],
            [
                'Method GuzzleHttp\Client::deleteAsync is called without connect_timeout option.',
                36,
            ],
            [
                'Method GuzzleHttp\Client::sendAsync is called without timeout option.',
                37,
            ],
            [
                'Method GuzzleHttp\Client::sendAsync is called without connect_timeout option.',
                37,
            ],
            [
                'Method GuzzleHttp\Client::requestAsync is called without timeout option.',
                38,
            ],
            [
                'Method GuzzleHttp\Client::requestAsync is called without connect_timeout option.',
                38,
            ],
        ]);
    }

    public function testCorrectCalls(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/CorrectCalls.php'], []);
    }
}

<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Guzzle\ClientCallWithoutTimeoutOptionRule;

use Efabrica\PHPStanRules\Rule\Guzzle\ClientCallWithoutTimeoutOptionRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class ClientCallWithoutTimeoutOptionRuleTest extends RuleTestCase
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
        return $this->getContainer()->getByType(ClientCallWithoutTimeoutOptionRule::class);
    }

    public function testNoOptionsWithClientAsPrivateProperty(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/NoOptionsWithClientAsPrivateProperty.php'], [
            [
                'Method GuzzleHttp\Client::get is called without timeout option.',
                21,
            ],
            [
                'Method GuzzleHttp\Client::post is called without timeout option.',
                22,
            ],
            [
                'Method GuzzleHttp\Client::put is called without timeout option.',
                23,
            ],
            [
                'Method GuzzleHttp\Client::head is called without timeout option.',
                24,
            ],
            [
                'Method GuzzleHttp\Client::patch is called without timeout option.',
                25,
            ],
            [
                'Method GuzzleHttp\Client::delete is called without timeout option.',
                26,
            ],
            [
                'Method GuzzleHttp\Client::send is called without timeout option.',
                27,
            ],
            [
                'Method GuzzleHttp\Client::request is called without timeout option.',
                28,
            ],
            [
                'Method GuzzleHttp\Client::getAsync is called without timeout option.',
                30,
            ],
            [
                'Method GuzzleHttp\Client::postAsync is called without timeout option.',
                31,
            ],
            [
                'Method GuzzleHttp\Client::putAsync is called without timeout option.',
                32,
            ],
            [
                'Method GuzzleHttp\Client::headAsync is called without timeout option.',
                33,
            ],
            [
                'Method GuzzleHttp\Client::patchAsync is called without timeout option.',
                34,
            ],
            [
                'Method GuzzleHttp\Client::deleteAsync is called without timeout option.',
                35,
            ],
            [
                'Method GuzzleHttp\Client::sendAsync is called without timeout option.',
                36,
            ],
            [
                'Method GuzzleHttp\Client::requestAsync is called without timeout option.',
                37,
            ],
        ]);
    }

    public function testEmptyArrayOptionsWithClientAsPrivateProperty(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/EmptyArrayOptionsWithClientAsPrivateProperty.php'], [
            [
                'Method GuzzleHttp\Client::get is called without timeout option.',
                21,
            ],
            [
                'Method GuzzleHttp\Client::post is called without timeout option.',
                22,
            ],
            [
                'Method GuzzleHttp\Client::put is called without timeout option.',
                23,
            ],
            [
                'Method GuzzleHttp\Client::head is called without timeout option.',
                24,
            ],
            [
                'Method GuzzleHttp\Client::patch is called without timeout option.',
                25,
            ],
            [
                'Method GuzzleHttp\Client::delete is called without timeout option.',
                26,
            ],
            [
                'Method GuzzleHttp\Client::send is called without timeout option.',
                27,
            ],
            [
                'Method GuzzleHttp\Client::request is called without timeout option.',
                28,
            ],
            [
                'Method GuzzleHttp\Client::getAsync is called without timeout option.',
                30,
            ],
            [
                'Method GuzzleHttp\Client::postAsync is called without timeout option.',
                31,
            ],
            [
                'Method GuzzleHttp\Client::putAsync is called without timeout option.',
                32,
            ],
            [
                'Method GuzzleHttp\Client::headAsync is called without timeout option.',
                33,
            ],
            [
                'Method GuzzleHttp\Client::patchAsync is called without timeout option.',
                34,
            ],
            [
                'Method GuzzleHttp\Client::deleteAsync is called without timeout option.',
                35,
            ],
            [
                'Method GuzzleHttp\Client::sendAsync is called without timeout option.',
                36,
            ],
            [
                'Method GuzzleHttp\Client::requestAsync is called without timeout option.',
                37,
            ],
        ]);
    }

    public function testCorrectTimeoutOptionWithClientAsPrivateProperty(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/CorrectTimeoutOptionWithClientAsPrivateProperty.php'], []);
    }
}

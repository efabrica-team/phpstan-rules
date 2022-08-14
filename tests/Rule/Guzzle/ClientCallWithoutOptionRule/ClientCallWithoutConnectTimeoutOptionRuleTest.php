<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Guzzle\ClientCallWithoutOptionRule;

final class ClientCallWithoutConnectTimeoutOptionRuleTest extends ClientCallWithoutOptionRuleTest
{
    protected function getServiceName(): string
    {
        return 'guzzleClientCallWithoutConnectTimeoutOptionRule';
    }

    protected function getOptionName(): string
    {
        return 'connect_timeout';
    }
}

<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Guzzle\ClientCallWithoutOptionRule;

final class ClientCallWithoutTimeoutOptionRuleTest extends ClientCallWithoutOptionRuleTest
{
    protected function getServiceName(): string
    {
        return 'guzzleClientCallWithoutTimeoutOptionRule';
    }

    protected function getOptionName(): string
    {
        return 'timeout';
    }
}

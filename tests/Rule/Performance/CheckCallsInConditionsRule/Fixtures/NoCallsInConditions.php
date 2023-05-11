<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures;

final class NoCallsInConditions
{
    private bool $bool;

    private string $string;

    public function __construct(bool $bool, string $string)
    {
        $this->bool = $bool;
        $this->string = $string;
    }

    public function doSomething(): bool
    {
        if ($this->bool || $this->string) {
            return true;
        }
        return false;
    }
}
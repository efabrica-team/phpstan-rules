<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures;

final class ElseIfCallsInConditions
{
    private bool $bool;

    private string $string;

    public function __construct(bool $bool, string $string)
    {
        $this->bool = $bool;
        $this->string = $string;
    }

    public function warnsInIfAndElseIf(): bool
    {
        if (file_exists($this->string) && $this->bool) {
            return true;
        } elseif (file_exists($this->string) && $this->bool) {
            return true;
        }

        return false;
    }

    public function warnsInElseIfWithXor(): bool
    {
        if ($this->bool) {
            return true;
        } elseif (file_exists($this->string) xor $this->bool) {
            return true;
        }

        return false;
    }
}

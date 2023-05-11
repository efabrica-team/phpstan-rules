<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Performance\CheckSlowCallsInConditionsRule\Fixtures;

final class SlowCallsInConditions
{
    private bool $bool;

    private string $string;

    public function __construct(bool $bool, string $string)
    {
        $this->bool = $bool;
        $this->string = $string;
    }

    public function fileExistsAsFirstInOr(): bool
    {
        if (file_exists($this->string) || $this->bool || $this->string) {
            return true;
        }
        return false;
    }

    public function fileExistsAsFirstInAnd(): bool
    {
        if (file_exists($this->string) && $this->bool && $this->string) {
            return true;
        }
        return false;
    }

    public function fileExistsAsSecondInAnd(): bool
    {
        if ($this->bool && file_exists($this->string) || $this->string) {
            return true;
        }
        return false;
    }

    public function fileExistsAsFirstInTwoPartAnd(): bool
    {
        if ($this->bool && (file_exists($this->string) || $this->string)) {
            return true;
        }
        return false;
    }

    public function fileExistsAsLastInOr(): bool
    {
        if ($this->bool || $this->string || file_exists($this->string)) {
            return true;
        }
        return false;
    }

    public function fileExistsAsLastInAnd(): bool
    {
        if ($this->bool && $this->string && file_exists($this->string)) {
            return true;
        }
        return false;
    }

    public function negatedFileExistsInAnd(): bool
    {
        if (!file_exists($this->string) && $this->bool && !$this->string) {
            return true;
        }
        return false;
    }
}

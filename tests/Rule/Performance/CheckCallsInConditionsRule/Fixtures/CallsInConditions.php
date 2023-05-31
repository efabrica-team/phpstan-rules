<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures;

use DateTime;
use Nette\Utils\Strings;

final class CallsInConditions
{
    private bool $bool;

    private string $string;

    private array $array;

    private CallsInConditions $callsInConditions;

    public function __construct(bool $bool, string $string, array $array, CallsInConditions $callsInConditions)
    {
        $this->bool = $bool;
        $this->string = $string;
        $this->array = $array;
        $this->callsInConditions = $callsInConditions;
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

    public function issetAsFirst(): bool
    {
        if (isset($this->array[$this->string]) && $this->array[$this->string]) {
            return true;
        }
        return false;
    }

    public function emptyAsFirst(): bool
    {
        if (empty($this->array) && empty($this->string) && $this->bool) {
            return true;
        }
        return false;
    }

    public function thisMethodCallFirst(): bool
    {
        if ($this->emptyAsFirst() && $this->bool) {
            return true;
        }
        return false;
    }

    public function methodCallFirst(): bool
    {
        if ($this->callsInConditions->emptyAsFirst() && $this->bool) {
            return true;
        }
        return false;
    }

    public function staticMethodInTheMiddle(): bool
    {
        if ($this->string && Strings::webalize($this->string) && $this->bool) {
            return true;
        }
        return false;
    }

    public function staticMethodComparedToStringInTheMiddle(): bool
    {
        if ($this->string && Strings::webalize($this->string) === 'foo-bar' && $this->bool) {
            return true;
        }
        return false;
    }

    public function staticMethodInJodaConditionInTheMiddle(): bool
    {
        if ($this->string && 'foo-bar' === Strings::webalize($this->string) && $this->bool) {
            return true;
        }
        return false;
    }

    public function onlyComparingWithMethodCalls(): bool
    {
        if ($this->fileExistsAsFirstInAnd() === false) {
            return true;
        }
        return false;
    }

    public function comparingResultOfMethodCallsFirst(): bool
    {
        if ($this->fileExistsAsFirstInAnd() == $this->fileExistsAsFirstInOr() && $this->string && $this->bool) {
            return true;
        }
        return false;
    }

    public function onlyAssignWithMethodCall(): bool
    {
        if ($file = $this->fileExistsAsFirstInAnd()) {
            return true;
        }
        return false;
    }

    public function assignResultOfMethodCallFirst(): bool
    {
        if ($file = $this->fileExistsAsFirstInAnd() && $this->string && $this->bool) {
            return true;
        }
        return false;
    }

    public function compareMethodCallInFirst(): bool
    {
        if ($this->createDateTime('-1 week') < new DateTime() && $this->string) {
            return true;
        }
        return false;
    }

    public function compareMethodCallInBoth(): bool
    {
        if ($this->createDateTime('-1 week') < new DateTime() && $this->createDateTime('+2 weeks') >= new DateTime()) {
            return true;
        }
        return false;
    }

    private function createDateTime(string $dateTime): DateTime
    {
        return new DateTime($dateTime);
    }
}

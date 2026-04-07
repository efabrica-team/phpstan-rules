<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures;

use DateTime;
use Nette\Utils\Strings;

final class MultipleCallsInOneIfNoFalsePositive
{
    private string $string;

    public function __construct(string $string)
    {
        $this->string = $string;
    }

    public function multipleCallsInOneIf(): bool
    {
        if (file_exists($this->string) && $this->createDateTime('-1 week') < new DateTime() && Strings::webalize($this->string) !== '') {
            return true;
        }

        return false;
    }

    private function createDateTime(string $dateTime): DateTime
    {
        return new DateTime($dateTime);
    }
}

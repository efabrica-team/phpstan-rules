<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\DisableMethodCallInContextRule\Source;

abstract class BaseClassWithDisabledMethod implements WithDisabledMethodInterface
{
    public function disabledMethod(): WithDisabledMethodInterface
    {
        return $this;
    }
}

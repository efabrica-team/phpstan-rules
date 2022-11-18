<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\DisableMethodCallInContextRule\Source;

class UncheckedClass
{
    public function disabledMethod(): UncheckedClass
    {
        return $this;
    }
}

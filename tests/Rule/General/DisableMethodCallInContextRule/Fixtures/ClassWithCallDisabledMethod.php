<?php

namespace Efabrica\PHPStanRules\Tests\Rule\General\DisableMethodCallInContextRule\Fixtures;

use Efabrica\PHPStanRules\Tests\Rule\General\DisableMethodCallInContextRule\Source\BaseClassWithCall;
use Efabrica\PHPStanRules\Tests\Rule\General\DisableMethodCallInContextRule\Source\CheckedClass;
use Efabrica\PHPStanRules\Tests\Rule\General\DisableMethodCallInContextRule\Source\UncheckedClass;

class ClassWithCallDisabledMethod extends BaseClassWithCall
{
    public function someMethod(): array
    {
        return [
            new CheckedClass(),
            (new CheckedClass())->disabledMethod(),
            (new UncheckedClass())->disabledMethod(),
        ];
    }

    public function checkedMethod(): array
    {
        return [
            new CheckedClass(),
            (new CheckedClass())->disabledMethod(),
            (new UncheckedClass())->disabledMethod(),
        ];
    }
}

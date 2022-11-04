<?php

namespace Efabrica\PHPStanRules\Tests\Rule\General\DisableCallMethodInObjectMethodRule\Fixtures;

use Efabrica\PHPStanRules\Tests\Rule\General\DisableCallMethodInObjectMethodRule\Source\BaseClassWithCall;
use Efabrica\PHPStanRules\Tests\Rule\General\DisableCallMethodInObjectMethodRule\Source\CheckedClass;
use Efabrica\PHPStanRules\Tests\Rule\General\DisableCallMethodInObjectMethodRule\Source\UncheckedClass;

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
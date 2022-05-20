<?php

namespace PHPSTORM_META {

    use Efabrica\PHPStanRules\Tests\Type\DynamicMethodReturnType\Source\Asdf;

    class ExtendedAsdf extends Asdf
    {

    }

    override(\Efabrica\PHPStanRules\Tests\Type\DynamicMethodReturnType\Source\Qwerty::post(),
        map([
            '' => 'string',
        ])
    );

    override(\Efabrica\PHPStanRules\Tests\Type\DynamicMethodReturnType\Source\Qwerty::asdf(),
        map([
            '' => '\PHPSTORM_META\ExtendedAsdf',
        ])
    );
}

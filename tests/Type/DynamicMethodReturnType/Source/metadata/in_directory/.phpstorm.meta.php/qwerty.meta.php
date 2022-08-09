<?php

namespace PHPSTORM_META {

    use Efabrica\PHPStanRules\Tests\Type\DynamicMethodReturnType\Source\Asdf;
    use Efabrica\PHPStanRules\Tests\Type\DynamicMethodReturnType\Source\Qwerty;

    class ExtendedAsdf extends Asdf
    {

    }

    override(
        Qwerty::post(),
        map([
            '' => 'string',
        ])
    );

    override(
        Qwerty::asdf(),
        map([
            '' => '\PHPSTORM_META\ExtendedAsdf',
        ])
    );
}

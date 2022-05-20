<?php

namespace Efabrica\PHPStanRules\Tests\Type\DynamicMethodReturnType\Source;

/**
 * @method post()
 * @method qwerty()
 */
class Asdf
{
    public function __call(string $name, $args)
    {

    }
}

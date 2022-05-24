<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Type\DynamicMethodReturnType\Source;

/**
 * @method post()
 * @method qwerty()
 */
final class Asdf
{
    public function __call(string $name, $args)
    {
    }
}

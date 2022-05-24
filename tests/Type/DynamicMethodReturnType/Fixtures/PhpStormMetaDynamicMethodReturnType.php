<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Type\DynamicMethodReturnType\Fixtures;

use Efabrica\PHPStanRules\Tests\Type\DynamicMethodReturnType\Source\Asdf;
use Efabrica\PHPStanRules\Tests\Type\DynamicMethodReturnType\Source\Qwerty;
use function PHPStan\Testing\assertType;

final class PhpStormMetaDynamicMethodReturnType
{
    public function asdf(): void
    {
        $asdf = new Asdf();
        assertType('int', $asdf->post());
        assertType(Qwerty::class, $asdf->qwerty());
    }

    public function qwerty(): void
    {
        $qwerty = new Qwerty();
        assertType('string', $qwerty->post());
        assertType('PHPSTORM_META\ExtendedAsdf', $qwerty->asdf());
    }
}

<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\RequiredParametersInMethodCallRule\Source;

final class Selection
{
    public function count(?string $column = null): int
    {
         return mt_rand(0, 100);
    }
}

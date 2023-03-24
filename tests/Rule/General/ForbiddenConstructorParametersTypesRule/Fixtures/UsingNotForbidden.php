<?php

namespace Efabrica\PHPStanRules\Tests\Rule\General\ForbiddenConstructorParametersTypesRule\Fixtures;

use Efabrica\PHPStanRules\Tests\Rule\General\ForbiddenConstructorParametersTypesRule\Source\ForbiddenType;
use Efabrica\PHPStanRules\Tests\Rule\General\ForbiddenConstructorParametersTypesRule\Source\NotForbiddenType;
use Efabrica\PHPStanRules\Tests\Rule\General\ForbiddenConstructorParametersTypesRule\Source\SomeInterface;

final class UsingNotForbidden implements SomeInterface
{
    private string $name;

    private NotForbiddenType $type;

    private int $count;

    public function __construct(string $name, NotForbiddenType $type, int $count)
    {
        $this->name = $name;
        $this->type = $type;
        $this->count = $count;
    }

    public function doNotCheckThisMethod(ForbiddenType $forbiddenType): void
    {
    }
}

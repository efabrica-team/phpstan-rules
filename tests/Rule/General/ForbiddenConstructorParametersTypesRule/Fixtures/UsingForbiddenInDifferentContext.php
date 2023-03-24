<?php

namespace Efabrica\PHPStanRules\Tests\Rule\General\ForbiddenConstructorParametersTypesRule\Fixtures;

use Efabrica\PHPStanRules\Tests\Rule\General\ForbiddenConstructorParametersTypesRule\Source\ForbiddenType;

final class UsingForbiddenInDifferentContext
{
    private string $name;

    private ForbiddenType $type;

    private int $count;

    public function __construct(string $name, ForbiddenType $type, int $count)
    {
        $this->name = $name;
        $this->type = $type;
        $this->count = $count;
    }
}

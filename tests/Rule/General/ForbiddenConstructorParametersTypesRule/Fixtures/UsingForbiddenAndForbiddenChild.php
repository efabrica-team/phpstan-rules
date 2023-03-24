<?php

namespace Efabrica\PHPStanRules\Tests\Rule\General\ForbiddenConstructorParametersTypesRule\Fixtures;

use Efabrica\PHPStanRules\Tests\Rule\General\ForbiddenConstructorParametersTypesRule\Source\ForbiddenChildType;
use Efabrica\PHPStanRules\Tests\Rule\General\ForbiddenConstructorParametersTypesRule\Source\ForbiddenType;
use Efabrica\PHPStanRules\Tests\Rule\General\ForbiddenConstructorParametersTypesRule\Source\SomeInterface;

final class UsingForbiddenAndForbiddenChild implements SomeInterface
{
    private string $name;

    private ForbiddenType $type1;

    private ForbiddenChildType $type2;

    private int $count;

    public function __construct(string $name, ForbiddenType $type1, ForbiddenChildType $type2, int $count)
    {
        $this->name = $name;
        $this->type1 = $type1;
        $this->type2 = $type2;
        $this->count = $count;
    }
}

<?php

namespace Efabrica\PHPStanRules\Tests\Rule\General\SchemaUnusedProperties\Fixtures\Schema;

use Efabrica\PHPStanRules\Tests\Rule\General\DisableMethodCallInContextRule\Source\BaseClassWithCall;

class ClassWithDefaultValues extends BaseClassWithCall
{
    private string $name;

    private bool $refreshBlocks;

    private ?string $note;

    private int $limit;

    public function __construct(
        string $name,
        bool $refreshBlocks = false,
        ?string $note = null,
        int $limit = 10
    ) {
        $this->name = $name;
        $this->refreshBlocks = $refreshBlocks;
        $this->note = $note;
        $this->limit = $limit;
    }

    public static function test(): void
    {
        // refreshBlocks: explicit false (equals default) and once true -> two different values, not reported
        // note: always explicit null which equals default -> treated as omitted, not reported here
        // limit: always explicit 20, different from default 10 -> reported
        $test = new ClassWithDefaultValues('a', false, null, 20);
        $test = new ClassWithDefaultValues('b', false, null, 20);
        $test = new ClassWithDefaultValues('c', true, null, 20);
    }
}

<?php

namespace Efabrica\PHPStanRules\Tests\Rule\General\SchemaNeverUsedProperties\Fixtures\Schema;

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
        $test = new ClassWithDefaultValues('a');
        $test = new ClassWithDefaultValues('b', false, null, 20);
        $test = new ClassWithDefaultValues('c', true, null, 20);
    }
}

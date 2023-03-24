<?php

namespace Efabrica\PHPStanRules\Tests\Rule\General\RequiredParametersInMethodCallRule\Fixtures;

use Efabrica\PHPStanRules\Tests\Rule\General\RequiredParametersInMethodCallRule\Source\Selection;

final class CallSelectionCount
{
    public function correct(): void
    {
        $selection = new Selection();
        $selection->count('*');
        $selection->count('id');
    }

    public function incorrect(): void
    {
        $selection = new Selection();
        $selection->count();
        $selection->count(null);
    }
}

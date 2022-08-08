<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Fixtures;

use Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Source\FirstTrait;
use Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Source\SecondTrait;
use Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Source\ThirdTrait;

final class AllTraitsWrong
{
    use FirstTrait;
    use SecondTrait;
    use ThirdTrait;
}

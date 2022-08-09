<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Fixtures;

use Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Source\FirstInterface;
use Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Source\FirstTrait;
use Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Source\SecondTrait;

final class OneTraitCorrectAndOneWrong implements FirstInterface
{
    use FirstTrait;
    use SecondTrait;
}

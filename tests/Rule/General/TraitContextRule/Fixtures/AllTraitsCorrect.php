<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Fixtures;

use Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Source\FirstInterface;
use Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Source\FirstTrait;
use Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Source\IntersectionContextTrait;
use Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Source\NoContextTrait;
use Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Source\SecondInterface;
use Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Source\SecondTrait;
use Efabrica\PHPStanRules\Tests\Rule\General\TraitContextRule\Source\UnionContextTrait;

final class AllTraitsCorrect implements FirstInterface, SecondInterface
{
    use FirstTrait;
    use SecondTrait;
    use UnionContextTrait;
    use IntersectionContextTrait;
    use NoContextTrait;
}

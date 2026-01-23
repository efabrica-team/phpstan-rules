<?php

namespace Efabrica\PHPStanRules\Tests\Rule\Nette\data;

use Nette\DI\Attributes\Inject;
use stdClass;

class InjectAttributePresenter
{
    #[Inject]
    public stdClass $service;
}

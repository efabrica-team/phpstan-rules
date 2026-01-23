<?php

namespace Efabrica\PHPStanRules\Tests\Rule\Nette\data;

use Nette\DI\Attributes\Inject;

class Service
{

}

class InjectAttributePresenter
{
    #[Inject]
    public Service $service;
}

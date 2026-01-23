<?php

namespace Efabrica\PHPStanRules\Tests\Rule\Nette\data;

class Service
{

}

class InjectPresenter
{
    /** @var Service @inject */
    public Service $service;
}
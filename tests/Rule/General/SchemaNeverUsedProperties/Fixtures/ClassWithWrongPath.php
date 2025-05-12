<?php

namespace Efabrica\PHPStanRules\Tests\Rule\General\SchemaNeverUsedProperties\Fixtures;

use Efabrica\PHPStanRules\Tests\Rule\General\DisableMethodCallInContextRule\Source\BaseClassWithCall;

class ClassWithWrongPath extends BaseClassWithCall
{

    private bool $propertyA;

    private bool $propertyB;

    private bool $propertyC;

     public function __construct(
         bool $propoetyA = false,
         bool $propoetyB = false,
         bool $propoetyC = false
     )
     {
         $this->propertyA = $propoetyA;
         $this->propertyB = $propoetyB;
         $this->propertyC = $propoetyC;
     }

     public static function test(): ClassWithWrongPath
     {
         return new ClassWithWrongPath();
     }
}

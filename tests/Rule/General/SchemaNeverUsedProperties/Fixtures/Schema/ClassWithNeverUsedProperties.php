<?php

namespace Efabrica\PHPStanRules\Tests\Rule\General\SchemaNeverUsedProperties\Fixtures\Schema;

use Efabrica\PHPStanRules\Tests\Rule\General\DisableMethodCallInContextRule\Source\BaseClassWithCall;

class ClassWithNeverUsedProperties extends BaseClassWithCall
{
    private bool $propertyA;

    private int $propertyB;

    private bool $propertyC;

     public function __construct(
         bool $propoetyA = false,
         int $propoetyB = 1,
         bool $propoetyC = false
     ) {
         $this->propertyA = $propoetyA;
         $this->propertyB = $propoetyB;
         $this->propertyC = $propoetyC;
     }

     public static function test(): ClassWithNeverUsedProperties
     {
         return new ClassWithNeverUsedProperties(true);
     }
}

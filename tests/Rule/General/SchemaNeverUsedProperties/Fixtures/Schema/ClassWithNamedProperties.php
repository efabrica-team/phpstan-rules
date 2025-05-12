<?php

namespace Efabrica\PHPStanRules\Tests\Rule\General\SchemaNeverUsedProperties\Fixtures\Schema;

use Efabrica\PHPStanRules\Tests\Rule\General\DisableMethodCallInContextRule\Source\BaseClassWithCall;

class ClassWithNamedProperties extends BaseClassWithCall
{
    private bool $propertyA;
    private bool $propertyB;
    private bool $propertyC;

     public function __construct(
         bool $propertyA = false,
         bool $propertyB = false,
         bool $propertyC = false
     )
     {
        $this->propertyA = $propertyA;
        $this->propertyB = $propertyB;
        $this->propertyC = $propertyC;
     }

     public static function test(): ?ClassWithNamedProperties
     {
         if (version_compare(PHP_VERSION, '8.0.0') >= 0) {
             return new ClassWithNamedProperties(propertyB: true);
         }
         return null;
     }
}

<?php

namespace Efabrica\PHPStanRules\Tests\Rule\General\SchemaUnusedProperties\Fixtures\Schema;

use Efabrica\PHPStanRules\Tests\Rule\General\DisableMethodCallInContextRule\Source\BaseClassWithCall;

class ClassWithNamedProperties extends BaseClassWithCall
{
    private bool $propertyA;

    private bool $propertyB;

    private bool $propertyC;

     public function __construct(
         string $propertyA = '',
         int $propertyB = 1,
         bool $propertyC = false
     ) {
         $this->propertyA = $propertyA;
         $this->propertyB = $propertyB;
         $this->propertyC = $propertyC;
     }

     public static function test(): void
     {
         if (version_compare(PHP_VERSION, '8.0.0') >= 0) {
             $test =  new ClassWithNamedProperties('', propertyB: 2, propertyC: true);
             $test =  new ClassWithNamedProperties('', propertyB: 1, propertyC: false);
         }
     }
}

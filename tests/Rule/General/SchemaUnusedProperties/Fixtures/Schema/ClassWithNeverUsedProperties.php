<?php

namespace Efabrica\PHPStanRules\Tests\Rule\General\SchemaUnusedProperties\Fixtures\Schema;

use Efabrica\PHPStanRules\Tests\Rule\General\DisableMethodCallInContextRule\Source\BaseClassWithCall;

class ClassWithNeverUsedProperties extends BaseClassWithCall
{
     public const CONSTANT = 3.14;

     private string $propertyA;

     private bool $propertyB;

     private array $propertyC;

     private int $propertyD;

     private float $propertyE;

     public function __construct(
         string $propertyA = '',
         bool $propertyB = false,
         array $propertyC = [],
         int $propertyD = 0,
         float $propertyE = 0.0
     ) {
         $this->propertyA = $propertyA;
         $this->propertyB = $propertyB;
         $this->propertyC = $propertyC;
         $this->propertyD = $propertyD;
         $this->propertyE = $propertyE;
     }

     public static function test(): void
     {
         $test = new ClassWithNeverUsedProperties('', false, [], 1, self::CONSTANT);
         $test = new ClassWithNeverUsedProperties('', false, [], 1, self::CONSTANT);
     }
}

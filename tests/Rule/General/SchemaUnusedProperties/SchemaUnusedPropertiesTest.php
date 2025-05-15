<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\SchemaUnusedProperties;

use Efabrica\PHPStanRules\Collector\Schema\SchemaDefinitions;
use Efabrica\PHPStanRules\Collector\Schema\SchemaUsage;
use Efabrica\PHPStanRules\Rule\Schema\UnusedProperties;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class SchemaUnusedPropertiesTest extends RuleTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../../../extension.neon',
            __DIR__ . '/rules.neon',
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getContainer()->getByType(UnusedProperties::class);
    }

    public function testCorrect(): void
    {
         $this->analyse([__DIR__ . '/Fixtures/Schema/ClassWithUsedProperties.php'], []);
    }

    public function testUnused(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/Schema/ClassWithNeverUsedProperties.php'], [
            [
                'Class "\Efabrica\PHPStanRules\Tests\Rule\General\SchemaUnusedProperties\Fixtures\Schema\ClassWithNeverUsedProperties" contains properties  "propertyA,propertyB,propertyC,propertyD,propertyE" called with same static values.',
                7,
            ],
        ]);
    }

    public function testNamed(): void
    {
        if (version_compare(PHP_VERSION, '8.0.0') >= 0) {
            $this->analyse([__DIR__ . '/Fixtures/Schema/ClassWithNamedProperties.php'], [
                [
                    'Class "\Efabrica\PHPStanRules\Tests\Rule\General\SchemaUnusedProperties\Fixtures\Schema\ClassWithNamedProperties" contains properties  "propertyA" called with same static values.'
                    , 7,
                ],
            ]);
        }
    }

    /**
     * @return array<Collector>
     */
    protected function getCollectors(): array
    {
        return [
            self::getContainer()->getByType(SchemaUsage::class),
            self::getContainer()->getByType(SchemaDefinitions::class),
        ];
    }
}

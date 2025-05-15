<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\SchemaNeverUsedProperties;

use Efabrica\PHPStanRules\Collector\Schema\SchemaDefinitions;
use Efabrica\PHPStanRules\Collector\Schema\SchemaUsage;
use Efabrica\PHPStanRules\Rule\Schema\NeverUsedProperties;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class SchemaNeverUsedPropertiesTest extends RuleTestCase
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
        return $this->getContainer()->getByType(NeverUsedProperties::class);
    }

    public function testCorrect(): void
    {
         $this->analyse([__DIR__ . '/Fixtures/Schema/ClassWithUsedProperties.php'], []);
    }

    public function testWrongPath(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/ClassWithWrongPath.php'], []);
    }

    public function testUnused(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/Schema/ClassWithNeverUsedProperties.php'], [
            [
                'Class "Efabrica\PHPStanRules\Tests\Rule\General\SchemaNeverUsedProperties\Fixtures\Schema\ClassWithNeverUsedProperties" contains never used properties "propoetyB,propoetyC".',
                7,
            ],
        ]);
    }

    public function testNamed(): void
    {
        if (version_compare(PHP_VERSION, '8.0.0') >= 0) {
            $this->analyse([__DIR__ . '/Fixtures/Schema/ClassWithNamedProperties.php'], [
                [
                    'Class "Efabrica\PHPStanRules\Tests\Rule\General\SchemaNeverUsedProperties\Fixtures\Schema\ClassWithNamedProperties" contains never used properties "propertyA,propertyC".',
                    7,
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

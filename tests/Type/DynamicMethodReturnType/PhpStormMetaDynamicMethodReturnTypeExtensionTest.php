<?php

namespace Efabrica\PHPStanRules\Tests\Type\DynamicMethodReturnType;

use PHPStan\Testing\TypeInferenceTestCase;

final class PhpStormMetaDynamicMethodReturnTypeExtensionTest extends TypeInferenceTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../../extension.neon',
            __DIR__ . '/../../../rules.neon',
            __DIR__ . '/config.neon',
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFileAsserts(
        string $assertType,
        string $file,
               ...$args
    ): void
    {
        $this->assertFileAsserts($assertType, $file, ...$args);
    }

    public function dataProvider(): iterable
    {
        yield from $this->gatherAssertTypes(__DIR__ . '/Fixtures/PhpStormMetaDynamicMethodReturnType.php');
    }
}

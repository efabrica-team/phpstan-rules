<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Type\JsonDecodeDynamicReturnTypeExtension\JsonDecodeForceAssoc;

use PHPStan\Testing\TypeInferenceTestCase;

final class JsonDecodeForceAssocTest extends TypeInferenceTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../../../extension.neon',
            __DIR__ . '/../../../../rules.neon',
        ];
    }

    public function dataProvider(): iterable
    {
        yield from $this->gatherAssertTypes(__DIR__ . '/Fixtures/JsonDecodeForceAssoc.php');
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
}

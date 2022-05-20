<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Type\JsonDecodeDynamicReturnTypeExtension\JsonDecodeForceAssoc\Fixtures;

use function PHPStan\Testing\assertType;

final class JsonDecodeForceAssoc
{
    public function emptyString(): void
    {
        $decodedJson = json_decode('', true);
        assertType('null', $decodedJson);
    }

    public function emptyJsonObject(): void
    {
        $decodedJson = json_decode('{}', true);
        assertType('array{}', $decodedJson);
    }

    public function emptyJsonArray(): void
    {
        $decodedJson = json_decode('[]', true);
        assertType('array{}', $decodedJson);
    }

    public function encodedInteger()
    {
        $decodedJson = json_decode('1', true);
        assertType('int', $decodedJson);
    }

    public function encodedString()
    {
        $decodedJson = json_decode('"asdf"', true);
        assertType('string', $decodedJson);
    }
}

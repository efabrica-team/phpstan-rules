<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\DisabledConcatenationWithTranslatedStringsRule\Source;

final class MyTranslator implements TranslatorInterface
{
    public function iAmTranslateMethod(string $message): string
    {
        return $message;
    }

    public static function iAmTranslateStaticMethod(string $message): string
    {
        return $message;
    }
}

<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\DisabledConcatenationWithTranslatedStringsRule\Source;

interface TranslatorInterface
{
    public function iAmTranslateMethod(string $message): string;

    public static function iAmTranslateStaticMethod(string $message): string;
}

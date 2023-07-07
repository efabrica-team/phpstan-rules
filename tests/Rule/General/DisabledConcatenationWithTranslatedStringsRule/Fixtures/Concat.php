<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\DisabledConcatenationWithTranslatedStringsRule\Fixtures;

use Efabrica\PHPStanRules\Tests\Rule\General\DisabledConcatenationWithTranslatedStringsRule\Source\MyTranslator;
use Efabrica\PHPStanRules\Tests\Rule\General\DisabledConcatenationWithTranslatedStringsRule\Source\TranslatorInterface;

final class Concat
{
    public function translateMessageAndConcatStringWithInterface(TranslatorInterface $translator, string $message): void
    {
        echo $translator->iAmTranslateMethod($message) . $message;
        echo MyTranslator::iAmTranslateStaticMethod($message) . $message;
        echo iAmTranslateFunction($message) . iAmTranslateFunction($message);
    }

    public function translateAndConcatWithMultipleStringsWithInterface(TranslatorInterface $translator, string $message): void
    {
        echo $message . $translator->iAmTranslateMethod($message) . 'qwerty';
        echo 'qwerty' . MyTranslator::iAmTranslateStaticMethod($message) . $message;
        echo 'qwerty' . iAmTranslateFunction($message) . 'qwerty';
    }

    public function translateMessageAndConcatStringWithImplementation(MyTranslator $translator, string $message): void
    {
        echo $translator->iAmTranslateMethod($message) . $message;
        echo MyTranslator::iAmTranslateStaticMethod($message) . $message;
        echo iAmTranslateFunction($message) . $message;
    }

    public function translateAndConcatWithMultipleStringsWithImplementation(MyTranslator $translator, string $message): void
    {
        echo $message . $translator->iAmTranslateMethod($message) . 'qwerty';
        echo 'qwerty' . MyTranslator::iAmTranslateStaticMethod($message) . $message;
        echo 'qwerty' . iAmTranslateFunction($message) . 'qwerty';
        echo $translator->iAmTranslateMethod($message) . MyTranslator::iAmTranslateStaticMethod($message) . iAmTranslateFunction($message);
    }
}

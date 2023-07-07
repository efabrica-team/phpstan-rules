<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\General\DisabledConcatenationWithTranslatedStringsRule\Fixtures;

use Efabrica\PHPStanRules\Tests\Rule\General\DisabledConcatenationWithTranslatedStringsRule\Source\MyTranslator;

final class Correct
{
    public function translateOnlyMessage(MyTranslator $translator, string $message): void
    {
        echo $translator->iAmTranslateMethod($message);
        echo MyTranslator::iAmTranslateStaticMethod($message);
        echo iAmTranslateFunction($message);
    }

    public function translateConcatedMessage(MyTranslator $translator, string $message): void
    {
        echo $translator->iAmTranslateMethod($message . '_success');
        echo MyTranslator::iAmTranslateStaticMethod($message . '_fail');
        echo iAmTranslateFunction($message . '_warning');
    }

    public function thisShouldBeOK(string $message): void
    {
        echo  'this should be OK ' . $this->helperMethod() . $message;
    }

    private function helperMethod(): string
    {
        return 'some string';
    }
}

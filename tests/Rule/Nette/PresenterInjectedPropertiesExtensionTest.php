<?php

namespace Efabrica\PHPStanRules\Tests\Rule\Nette;

use Efabrica\PHPStanRules\Rule\Nette\PresenterInjectedPropertiesExtension;
use Efabrica\PHPStanRules\Rule\Tomaj\NetteApi\InputParamNameRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\TestCase;

use PHPStan\Testing\LevelsTestCase;
use const PHP_VERSION_ID;

class PresenterInjectedPropertiesExtensionTest extends LevelsTestCase
{

    public function dataTopics(): array
    {
        if (PHP_VERSION_ID < 70400) {
            self::markTestSkipped('Only for PHP 7.4+');
        }

        $topics = [
            ['presenterInject'],
        ];

        if (PHP_VERSION_ID >= 80000) {
            $topics[] = ['presenterInjectAttribute'];
        }

        return $topics;
    }

    public function getDataPath(): string
    {
        return __DIR__ . '/data';
    }

    public function getPhpStanExecutablePath(): string
    {
        return __DIR__ . '/../../../vendor/bin/phpstan';
    }

    public function getPhpStanConfigPath(): ?string
    {
        return __DIR__ . '/phpstan.neon';
    }

}
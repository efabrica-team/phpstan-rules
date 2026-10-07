<?php

namespace Efabrica\PHPStanRules\Tests\Rule\Nette;

use PHPStan\Testing\LevelsTestCase;
use const PHP_VERSION_ID;

class PresenterInjectedPropertiesExtensionTest extends LevelsTestCase
{
    public static function dataTopics(): array
    {
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

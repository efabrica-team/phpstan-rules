<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule;

use Efabrica\PHPStanRules\Rule\Performance\CheckCallsInConditionsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class CheckCallsInConditionsRuleTest extends RuleTestCase
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
        return $this->getContainer()->getByType(CheckCallsInConditionsRule::class);
    }

    public function testNoCallsInConditions(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/NoCallsInConditions.php'], []);
    }

    public function testCallsInConditions(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/CallsInConditions.php'], [
            [
                'Performance: "file_exists()" is called in condition before faster expressions. Move it to the end.',
                29,
            ],
            [
                'Performance: "file_exists()" is called in condition before faster expressions. Move it to the end.',
                37,
            ],
            [
                'Performance: "file_exists()" is called in condition before faster expressions. Move it to the end.',
                45,
            ],
            [
                'Performance: "file_exists()" is called in condition before faster expressions. Move it to the end.',
                53,
            ],
            [
                'Performance: "file_exists()" is called in condition before faster expressions. Move it to the end.',
                77,
            ],
            [
                'Performance: "$this(Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions)->emptyAsFirst()" is called in condition before faster expressions. Move it to the end.',
                101,
            ],
            [
                'Performance: "Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions->emptyAsFirst()" is called in condition before faster expressions. Move it to the end.',
                109,
            ],
            [
                'Performance: "Nette\Utils\Strings::webalize()" is called in condition before faster expressions. Move it to the end.',
                117,
            ],
            [
                'Performance: "Nette\Utils\Strings::webalize()" is called in condition before faster expressions. Move it to the end.',
                125,
            ],
            [
                'Performance: "Nette\Utils\Strings::webalize()" is called in condition before faster expressions. Move it to the end.',
                133,
            ],
            [
                'Performance: "$this(Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions)->fileExistsAsFirstInAnd()" is called in condition before faster expressions. Move it to the end.',
                149,
            ],
            [
                'Performance: "$this(Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions)->fileExistsAsFirstInOr()" is called in condition before faster expressions. Move it to the end.',
                149,
            ],
            [
                'Performance: "$this(Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions)->fileExistsAsFirstInAnd()" is called in condition before faster expressions. Move it to the end.',
                165,
            ],
        ]);
    }
}

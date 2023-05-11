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
                'Performance: "file_exists()" is called in condition before expressions which seem to be faster.',
                29,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "file_exists()" is called in condition before expressions which seem to be faster.',
                37,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "file_exists()" is called in condition before expressions which seem to be faster.',
                45,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "file_exists()" is called in condition before expressions which seem to be faster.',
                53,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "file_exists()" is called in condition before expressions which seem to be faster.',
                77,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "$this(Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions)->emptyAsFirst()" is called in condition before expressions which seem to be faster.',
                101,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions->emptyAsFirst()" is called in condition before expressions which seem to be faster.',
                109,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "Nette\Utils\Strings::webalize()" is called in condition before expressions which seem to be faster.',
                117,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "Nette\Utils\Strings::webalize()" is called in condition before expressions which seem to be faster.',
                125,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "Nette\Utils\Strings::webalize()" is called in condition before expressions which seem to be faster.',
                133,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "$this(Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions)->fileExistsAsFirstInAnd()" is called in condition before expressions which seem to be faster.',
                149,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "$this(Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions)->fileExistsAsFirstInOr()" is called in condition before expressions which seem to be faster.',
                149,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "$this(Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions)->fileExistsAsFirstInAnd()" is called in condition before expressions which seem to be faster.',
                165,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
        ]);
    }
}

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
                30,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "file_exists()" is called in condition before expressions which seem to be faster.',
                38,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "file_exists()" is called in condition before expressions which seem to be faster.',
                46,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "file_exists()" is called in condition before expressions which seem to be faster.',
                54,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "file_exists()" is called in condition before expressions which seem to be faster.',
                78,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "$this(Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions)->emptyAsFirst()" is called in condition before expressions which seem to be faster.',
                102,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions->emptyAsFirst()" is called in condition before expressions which seem to be faster.',
                110,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "Nette\Utils\Strings::webalize()" is called in condition before expressions which seem to be faster.',
                118,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "Nette\Utils\Strings::webalize()" is called in condition before expressions which seem to be faster.',
                126,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "Nette\Utils\Strings::webalize()" is called in condition before expressions which seem to be faster.',
                134,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "$this(Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions)->fileExistsAsFirstInAnd()" is called in condition before expressions which seem to be faster.',
                150,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "$this(Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions)->fileExistsAsFirstInOr()" is called in condition before expressions which seem to be faster.',
                150,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "$this(Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions)->fileExistsAsFirstInAnd()" is called in condition before expressions which seem to be faster.',
                166,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "$this(Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions)->createDateTime()" is called in condition before expressions which seem to be faster.',
                174,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
        ]);
    }
}

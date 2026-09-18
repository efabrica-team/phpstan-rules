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
                'Performance: "file_exists()" is called in condition before faster expression "$this->bool".',
                30,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "file_exists()" is called in condition before faster expression "$this->bool".',
                38,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "file_exists()" is called in condition before faster expression "$this->string".',
                46,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "file_exists()" is called in condition before faster expression "$this->string".',
                54,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "file_exists()" is called in condition before faster expression "$this->bool".',
                78,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "$this(Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions)->emptyAsFirst()" is called in condition before faster expression "$this->bool".',
                102,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions->emptyAsFirst()" is called in condition before faster expression "$this->bool".',
                110,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "Nette\Utils\Strings::webalize()" is called in condition before faster expression "$this->bool".',
                118,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "Nette\Utils\Strings::webalize()" is called in condition before faster expression "$this->bool".',
                126,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "Nette\Utils\Strings::webalize()" is called in condition before faster expression "$this->bool".',
                134,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "$this(Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions)->fileExistsAsFirstInAnd()" is called in condition before faster expression "$this->string".',
                150,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "$this(Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions)->fileExistsAsFirstInOr()" is called in condition before faster expression "$this->string".',
                150,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "$this(Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions)->fileExistsAsFirstInAnd()" is called in condition before faster expression "$this->string".',
                166,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "$this(Efabrica\PHPStanRules\Tests\Rule\Performance\CheckCallsInConditionsRule\Fixtures\CallsInConditions)->createDateTime()" is called in condition before faster expression "$this->string".',
                174,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "file_exists()" is called in condition before faster expression "$this->bool".',
                190,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
        ]);
    }

    public function testCallsInElseIfConditions(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/ElseIfCallsInConditions.php'], [
            [
                'Performance: "file_exists()" is called in condition before faster expression "$this->bool".',
                21,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "file_exists()" is called in condition before faster expression "$this->bool".',
                23,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
            [
                'Performance: "file_exists()" is called in condition before faster expression "$this->bool".',
                34,
                'Move faster expressions to the beginning of the condition and calls to the end.',
            ],
        ]);
    }

    public function testMultipleCallsInOneIfDoNotReportFalsePositive(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/MultipleCallsInOneIfNoFalsePositive.php'], []);
    }
}

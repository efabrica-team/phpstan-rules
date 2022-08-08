<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Tomaj\NetteApi\InputParamNameRule;

use Efabrica\PHPStanRules\Rule\Tomaj\NetteApi\InputParamNameRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class InputParamNameRuleTest extends RuleTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../../../../extension.neon',
            __DIR__ . '/../../../../../rules.neon',
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getContainer()->getByType(InputParamNameRule::class);
    }

    public function testNoTimeoutWithClientAsPrivateProperty(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/SomeHandler.php'], [
            [
                'Incorrect parameter name "incorrect-get-name". Use "incorrect_get_name" instead.',
                22,
            ],
            [
                'Incorrect parameter name "incorrect-post-name". Use "incorrect_post_name" instead.',
                24,
            ],
            [
                'Incorrect parameter name "incorrect-json-input-param-name". Use "incorrect_json_input_param_name" instead.',
                25,
            ],
            [
                'Incorrect parameter name "incorrect-PUT-name". Use "incorrect_PUT_name" instead.',
                26,
            ],
        ]);
    }
}

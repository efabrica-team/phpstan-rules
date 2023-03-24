<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\Guzzle;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ObjectType;

/**
 * @implements Rule<MethodCall>
 */
final class ClientCallWithoutOptionRule implements Rule
{
    /**
     * @var array<string, int> method name => position of $options parameter (indexed from 0)
     */
    private array $methodOptionArgPosition = [
        'get' => 1,
        'post' => 1,
        'put' => 1,
        'head' => 1,
        'patch' => 1,
        'delete' => 1,
        'send' => 1,
        'request' => 2,
        'getAsync' => 1,
        'postAsync' => 1,
        'putAsync' => 1,
        'headAsync' => 1,
        'patchAsync' => 1,
        'deleteAsync' => 1,
        'sendAsync' => 1,
        'requestAsync' => 2,
    ];

    /** @var string[] */
    private array $optionNames;

    /**
     * @param string[] $optionNames
     */
    public function __construct(array $optionNames)
    {
        $this->optionNames = $optionNames;
    }

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();
        $callerType = $scope->getType($node->var);

        if (!$callerType instanceof ObjectType || !$callerType->isInstanceOf('GuzzleHttp\Client')->yes()) {
            return [];
        }

        $methodName = $this->getNameValue($node->name, $scope);
        if (!isset($this->methodOptionArgPosition[$methodName])) {
            return [];
        }

        $argPosition = $this->methodOptionArgPosition[$methodName];
        $argAtPosition = $node->getArgs()[$argPosition] ?? null;

        $errors = [];
        if ($argAtPosition === null) {
            foreach ($this->optionNames as $optionName) {
                $errors[] = RuleErrorBuilder::message('Method GuzzleHttp\Client::' . $methodName . ' is called without ' . $optionName . ' option.')->file($file)->line($node->getStartLine())->build();
            }
            return $errors;
        }

        $argAtPositionType = ($scope->getType($argAtPosition->value));
        if (!$argAtPositionType instanceof ConstantArrayType) {
            return $errors;
        }
        $optionalKeys = $argAtPositionType->getOptionalKeys();
        $requiredOptions = [];
        $optionalOptions = [];
        foreach ($argAtPositionType->getKeyTypes() as $i => $keyType) {
            if (in_array($i, $optionalKeys, true)) {
                $optionalOptions[] = $keyType->getValue();
            } else {
                $requiredOptions[] = $keyType->getValue();
            }
        }

        foreach ($this->optionNames as $optionName) {
            if (in_array($optionName, $requiredOptions, true)) {
                continue;
            }
            if (!in_array($optionName, $optionalOptions, true)) {
                $errors[] = RuleErrorBuilder::message('Method GuzzleHttp\Client::' . $methodName . ' is called without ' . $optionName . ' option.')->file($file)->line($node->getStartLine())->build();
            } else {
                $errors[] = RuleErrorBuilder::message('Method GuzzleHttp\Client::' . $methodName . ' is possibly called without ' . $optionName . ' option.')->file($file)->line($node->getStartLine())->build();
            }
        }
        return $errors;
    }

    /**
     * @param Identifier|Expr $name
     */
    private function getNameValue($name, Scope $scope): ?string
    {
        if ($name instanceof Identifier) {
            return $name->toString();
        }
        $nameType = $scope->getType($name);
        if ($nameType instanceof ConstantStringType) {
            return $nameType->getValue();
        }
        return null;
    }
}

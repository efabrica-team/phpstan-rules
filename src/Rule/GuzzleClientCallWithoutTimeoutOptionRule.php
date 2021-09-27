<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\ObjectType;
use Symplify\Astral\NodeValue\NodeValueResolver;

final class GuzzleClientCallWithoutTimeoutOptionRule implements Rule
{
    private NodeValueResolver $nodeValueResolver;

    /**
     * @var array<string, int> method name => position of $options parameter (indexed from 0)
     */
    private array $methodOptionArgPosition = [
        'send' => 1,
        'sendAsync' => 1,
        'request' => 2,
        'requestAsync' => 2,
        'get' => 1,
        'head' => 1,
        'put' => 1,
        'post' => 1,
        'patch' => 1,
        'delete' => 1,
        'getAsync' => 1,
        'headAsync' => 1,
        'putAsync' => 1,
        'postAsync' => 1,
        'patchAsync' => 1,
        'deleteAsync' => 1,
    ];

    public function __construct(NodeValueResolver $nodeValueResolver)
    {
        $this->nodeValueResolver = $nodeValueResolver;
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
        $callerType = $scope->getType($node->var);
        if (!$callerType instanceof ObjectType || !$callerType->isInstanceOf('GuzzleHttp\Client')->yes()) {
            return [];
        }

        $methodName = $node->name->name;
        if (!isset($this->methodOptionArgPosition[$methodName])) {
            return [];
        }

        $argPosition = $this->methodOptionArgPosition[$methodName];
        $argAtPosition = $node->args[$argPosition] ?? null;
        if ($argAtPosition === null) {
            return [
                'Method GuzzleHttp\Client::' . $methodName . ' is called without timeout optionx',
            ];
        }

        $options = $this->nodeValueResolver->resolveWithScope($argAtPosition->value, $scope);
        if (is_array($options)) {
            if (!isset($options['timeout'])) {
                return [
                    'Method GuzzleHttp\Client::' . $methodName . ' is called without timeout option',
                ];
            }
            return [];
        }

        return [
            'Method GuzzleHttp\Client::' . $methodName . ' is probably called without timeout option',
        ];
    }
}

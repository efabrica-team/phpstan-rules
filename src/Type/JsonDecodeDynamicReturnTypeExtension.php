<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Type;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ArrayType;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;
use PHPStan\Type\Php\JsonThrowOnErrorDynamicReturnTypeExtension;
use PHPStan\Type\Type;
use Symplify\Astral\NodeValue\NodeValueResolver;

class JsonDecodeDynamicReturnTypeExtension extends JsonThrowOnErrorDynamicReturnTypeExtension
{
//    public function __construct(private NodeValueResolver $nodeValueResolver, private ReflectionProvider $reflectionProvider)
//    {
//    }

    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {

        var_dump('som u mna s ' . $functionReflection->getName());
        return $functionReflection->getName() === 'json_decode';
    }

    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        var_dump('x');exit;

        $argValueExpression = $functionCall->getArgs()[0]->value;
        $argValue = $this->nodeValueResolver->resolveWithScope($argValueExpression, $scope);
        $associative = isset($functionCall->getArgs()[1]) ? $this->nodeValueResolver->resolveWithScope($functionCall->getArgs()[1]->value, $scope) : null;

        $type = new MixedType();
        if ($argValue === '') {
            $type = new NullType();
        } elseif (is_numeric($argValue)) {
            $type = new IntegerType();
        } elseif ($argValue === '{}') {
            $type = $associative === true ? new ArrayType(new MixedType(), new MixedType()) : new MixedType();  // TODO stdclass in else
        }

        var_dump($argValue);
        var_dump(get_class($type));

        return $type;
    }
}

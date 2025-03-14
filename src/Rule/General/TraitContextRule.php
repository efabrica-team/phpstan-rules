<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\General;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use ReflectionClass;

/**
 * @implements Rule<ClassLike>
 */
final class TraitContextRule implements Rule
{
    private TypeStringResolver $typeStringResolver;

    public function __construct(TypeStringResolver $typeStringResolver)
    {
        $this->typeStringResolver = $typeStringResolver;
    }

    public function getNodeType(): string
    {
        return ClassLike::class;
    }

    /**
     * @param ClassLike $classLike
     */
    public function processNode(Node $classLike, Scope $scope): array
    {
        $namespacedName = $classLike->namespacedName;
        if ($namespacedName === null) {
            return [];
        }

        $className = $namespacedName->toString();
        $classType = new ObjectType($className);

        $file = $scope->getFile();
        $errors = [];
        foreach ($classLike->getTraitUses() as $traitUse) {
            foreach ($traitUse->traits as $usedTraitName) {
                /** @var class-string $usedTrait */
                $usedTrait = $usedTraitName->toString();
                $reflectionClass = new ReflectionClass($usedTrait);
                $comment = $reflectionClass->getDocComment();
                if ($comment === false) {
                    continue;
                }

                preg_match('/@context (?P<contextType>.*)/', $comment, $match);
                if (!isset($match['contextType'])) {
                    continue;
                }

                $contextType = $this->typeStringResolver->resolve($match['contextType']);
                if ($contextType->accepts($classType, true)->no()) {
                    $errors[] = RuleErrorBuilder::message('Trait ' . $usedTrait . ' is used in wrong context.')->file($file)->line($traitUse->getStartLine())->build();
                }
            }
        }

        return $errors;
    }
}

<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Rule\General;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use function preg_match;

/**
 * @implements Rule<ClassLike>
 */
final class TraitContextRule implements Rule
{
    private TypeStringResolver $typeStringResolver;

    private ReflectionProvider $reflectionProvider;

    public function __construct(TypeStringResolver $typeStringResolver, ReflectionProvider $reflectionProvider)
    {
        $this->typeStringResolver = $typeStringResolver;
        $this->reflectionProvider = $reflectionProvider;
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
                $usedTrait = $usedTraitName->toString();
                if (!$this->reflectionProvider->hasClass($usedTrait)) {
                    continue;
                }
                $comment = $this->reflectionProvider->getClass($usedTrait)->getNativeReflection()->getDocComment();
                if ($comment === false) {
                    continue;
                }

                preg_match('/@context (?P<contextType>.*)/', $comment, $match);
                if (!isset($match['contextType'])) {
                    continue;
                }

                $contextType = $this->typeStringResolver->resolve($match['contextType']);
                if ($contextType->accepts($classType, true)->no()) {
                    $errors[] = RuleErrorBuilder::message('Trait ' . $usedTrait . ' is used in wrong context.')
                        ->identifier('efabrica.traitContext')
                        ->file($file)
                        ->line($traitUse->getStartLine())
                        ->build();
                }
            }
        }

        return $errors;
    }
}

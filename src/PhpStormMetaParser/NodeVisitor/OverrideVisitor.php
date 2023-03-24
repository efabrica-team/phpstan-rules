<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\PhpStormMetaParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeVisitorAbstract;

final class OverrideVisitor extends NodeVisitorAbstract
{
    /** @var array<string, string[]> */
    private array $uses = [];

    /** @var array<string, array<string, string>> */
    private array $classesMethodsAndTypes = [];

    public function enterNode(Node $node)
    {
        if ($node instanceof Use_) {
            foreach ($node->uses as $use) {
                $this->uses[$use->getAlias()->name] = $use->name->parts;
            }
            return null;
        }

        if (!$node instanceof FuncCall) {
            return null;
        }

        if (!$node->name instanceof Name) {
            return null;
        }

        if ((string)$node->name !== 'override') {
            return null;
        }

        if (!isset($node->getArgs()[1])) {
            return null;
        }

        $call = $node->getArgs()[0]->value;
        if (!$call instanceof StaticCall) {
            return null;
        }

        if (!$call->class instanceof Name || !$call->name instanceof Identifier) {
            return null;
        }

        $classNameParts = $call->class->parts;
        $alias = $classNameParts[0] ?? null;

        if ($alias && isset($this->uses[$alias])) {
            unset($classNameParts[0]);
            $classNameParts = array_merge($this->uses[$alias], $classNameParts);
        }

        $class = implode('\\', $classNameParts);
        $method = $call->name->name;

        $map = $node->getArgs()[1]->value;
        if (!$map instanceof FuncCall) {
            return null;
        }

        $mapArgs = $map->getArgs()[0]->value;
        if (!$mapArgs instanceof Array_) {
            return null;
        }

        /** @var ArrayItem $mapArg */
        foreach ($mapArgs->items as $mapArg) {
            if ($mapArg->key instanceof String_ && $mapArg->key->value === '' && $mapArg->value instanceof String_) {
                $value = implode('|', array_filter(explode('|', $mapArg->value->value), function ($item) {
                    return $item !== '@'; // TODO if @ is used we need to implement it in PhpStormMetaDynamicMethodReturnTypeExtension
                }));

                if ($value !== '') {
                    $this->classesMethodsAndTypes[$class][$method] = $value;
                }
            }
        }
        return null;
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function getClasssesMethodsAndTypes(): array
    {
        return $this->classesMethodsAndTypes;
    }
}

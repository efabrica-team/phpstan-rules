<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\PhpStormMetaParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeVisitorAbstract;

final class OverrideVisitor extends NodeVisitorAbstract
{
    private array $uses = [];

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

        if ((string)$node->name !== 'override') {
            return null;
        }

        if (!isset($node->args[1])) {
            return null;
        }

        $call = $node->args[0]->value;
        if (!$call instanceof StaticCall) {
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

        $map = $node->args[1]->value;
        if (!$map instanceof FuncCall) {
            return null;
        }

        $mapArgs = $map->args[0]->value;
        if (!$mapArgs instanceof Array_) {
            return null;
        }

        /** @var ArrayItem $mapArg */
        foreach ($mapArgs->items as $mapArg) {
            if ($mapArg->key instanceof String_ && $mapArg->key->value === '' && $mapArg->value instanceof String_) {
                $this->classesMethodsAndTypes[$class][$method] = $mapArg->value->value;
            }
        }
    }

    public function getClasssesMethodsAndTypes(): array
    {
        return $this->classesMethodsAndTypes;
    }
}

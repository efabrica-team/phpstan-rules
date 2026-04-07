<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Performance\UseNetteDatabaseSelectionFetchTogetherWithLimitRule\Fixtures;

use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

final class NetteDatabaseSelection
{
    public function fetchWithNoOtherMethod(Selection $selection): ?ActiveRow
    {
        return $selection->fetch();
    }

    public function fetchWithSomeOtherMethodsWithoutLimit(Selection $selection): ?ActiveRow
    {
        return $selection->where(['id' => 1])->fetch();
    }

    public function fetchWithLimit1(Selection $selection): ?ActiveRow
    {
        return $selection->limit(1)->fetch();
    }

    public function fetchWithLimit10(Selection $selection): ?ActiveRow
    {
        return $selection->limit(10)->fetch();
    }

    public function fetchWithLimit1nAnotherPlace(Selection $selection): ?ActiveRow
    {
        return $selection->limit(1)->where(['id' => 1])->fetch();
    }

    public function fetchWithLimit1NotFluent(Selection $selection, array $where = []): ?ActiveRow
    {
        $selection->limit(1);
        if ($where !== []) {
            $selection->where($where);
        }
        return $selection->fetch();
    }

    public function fetchWithConditionalLimit1NotFluent(Selection $selection, array $where = []): ?ActiveRow
    {
        if ($where !== []) {
            $selection->limit(1);
            $selection->where($where);
        }
        return $selection->fetch();
    }

    public function fetchWithoutLimitOnCurrentSelection(Selection $selectionA, Selection $selectionB): ?ActiveRow
    {
        $selectionA->limit(1);
        return $selectionB->fetch();
    }

    public function fetchWithFluentLimitOnCurrentSelection(Selection $selectionA, Selection $selectionB): ?ActiveRow
    {
        $selectionA->limit(1);
        return $selectionB->where(['id' => 1])->limit(1)->fetch();
    }

    public function fetchWithNonFluentLimitOnCurrentSelection(Selection $selectionA, Selection $selectionB): ?ActiveRow
    {
        $selectionB->limit(1);
        $selectionA->where(['id' => 1]);
        return $selectionB->fetch();
    }
}

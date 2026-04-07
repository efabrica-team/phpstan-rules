<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Performance\UseArrayComparisonInsteadOfCountInConditionRule\Fixtures;

final class Correct
{
    /** @var int[] */
    private array $items;

    /**
     * @param int[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function useArrayComparison(): bool
    {
        if ($this->items !== []) {
            return true;
        }

        if ($this->items === []) {
            return false;
        }

        return false;
    }

    public function countOutsideCondition(): int
    {
        return count($this->items);
    }

    public function compareToDifferentNumber(): bool
    {
        if (count($this->items) > 1) {
            return true;
        }

        return false;
    }
}

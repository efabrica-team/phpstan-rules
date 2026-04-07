<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Performance\UseArrayComparisonInsteadOfCountInConditionRule\Fixtures;

final class Wrong
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

    public function countUsedAsBoolean(): bool
    {
        if (count($this->items)) {
            return true;
        }

        return false;
    }

    public function negatedCountUsedAsBoolean(): bool
    {
        if (!count($this->items)) {
            return true;
        }

        return false;
    }

    public function greaterThanZero(): bool
    {
        if (count($this->items) > 0) {
            return true;
        }

        return false;
    }

    public function greaterOrEqualOne(): bool
    {
        if (count($this->items) >= 1) {
            return true;
        }

        return false;
    }

    public function notEqualZero(): bool
    {
        if (count($this->items) != 0) {
            return true;
        }

        return false;
    }

    public function notIdenticalZero(): bool
    {
        if (count($this->items) !== 0) {
            return true;
        }

        return false;
    }

    public function identicalZero(): bool
    {
        if (count($this->items) === 0) {
            return true;
        }

        return false;
    }

    public function equalZero(): bool
    {
        if (count($this->items) == 0) {
            return true;
        }

        return false;
    }

    public function smallerOrEqualZero(): bool
    {
        if (count($this->items) <= 0) {
            return true;
        }

        return false;
    }

    public function smallerThanOne(): bool
    {
        if (count($this->items) < 1) {
            return true;
        }

        return false;
    }

    public function reversedGreaterThanZero(): bool
    {
        if (0 < count($this->items)) {
            return true;
        }

        return false;
    }

    public function reversedIdenticalZero(): bool
    {
        if (0 === count($this->items)) {
            return true;
        }

        return false;
    }

    public function inElseIf(): bool
    {
        if ($this->items === []) {
            return false;
        } elseif (count($this->items) !== 0) {
            return true;
        }

        return false;
    }
}

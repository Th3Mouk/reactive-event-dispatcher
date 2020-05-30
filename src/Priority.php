<?php

declare(strict_types=1);

namespace Th3Mouk\ReactiveEventDispatcher;

/**
 * @psalm-immutable
 */
final class Priority
{
    private const MIN_VALUE = -1024;
    private const MAX_VALUE = 1024;

    public int $value;

    private function __construct(int $value)
    {
        if ($value < self::MIN_VALUE || $value > self::MAX_VALUE) {
            throw new \InvalidArgumentException('Priority value must be between ' . self::MIN_VALUE . ' and ' . self::MAX_VALUE);
        }

        $this->value = $value;
    }

    public static function fromInt(int $value): self
    {
        return new self($value);
    }
}

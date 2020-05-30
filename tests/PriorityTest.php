<?php

declare(strict_types=1);

namespace Th3Mouk\ReactiveEventDispatcher\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Th3Mouk\ReactiveEventDispatcher\Priority;

class PriorityTest extends TestCase
{
    public function testFromIntWithInsufficientValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Priority::fromInt(-2000);
    }

    public function testFromIntWithTooHighValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Priority::fromInt(2000);
    }

    public function testFromIntWithCorrectValue(): void
    {
        $priority = Priority::fromInt(0);

        $this->assertInstanceOf(Priority::class, $priority);
    }
}

<?php

declare(strict_types=1);

namespace Th3Mouk\ReactiveEventDispatcher\Tests;

use PHPUnit\Framework\TestCase;
use Th3Mouk\ReactiveEventDispatcher\EventCorrelation;
use Th3Mouk\ReactiveEventDispatcher\Priority;

class EventCorrelationTest extends TestCase
{
    public function testCreate(): void
    {
        $eventCorrelation = EventCorrelation::create(
            'event-fqcn',
            'listener-fqcn',
            Priority::fromInt(0)
        );

        $this->assertInstanceOf(EventCorrelation::class, $eventCorrelation);
    }
}

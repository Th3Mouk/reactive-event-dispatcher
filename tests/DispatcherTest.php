<?php

declare(strict_types=1);

namespace Th3Mouk\ReactiveEventDispatcher\Tests;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use Rx\Observable;
use Th3Mouk\ReactiveEventDispatcher\Dispatcher;
use Th3Mouk\ReactiveEventDispatcher\Event;
use Th3Mouk\ReactiveEventDispatcher\Listener;

class DispatcherTest extends TestCase
{
    protected function setUp(): void
    {
        $this->listener = new class implements Listener {
            public bool $has_been_processed = false;

            public function process(Event $event): Observable
            {
                return Observable::of('obs')
                    ->do(function (): void {
                        $this->has_been_processed = true;
                    });
            }
        };

        $this->listener_provider = new class($this->listener) implements ListenerProviderInterface {
            private Listener $listener;

            public function __construct(Listener $listener)
            {
                $this->listener = $listener;
            }

            /** @return iterable<callable> */
            public function getListenersForEvent(object $event): iterable
            {
                yield [$this->listener, 'process'];
            }
        };
    }

    public function testDispatch(): void
    {
        $event = new class implements Event {
        };

        (new Dispatcher($this->listener_provider))
            ->dispatch((new $event()))
            ->subscribe();

        $this->assertTrue($this->listener->has_been_processed);
    }

    public function testDispatchThrowingWithIncorrectEvent(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $event = new class {
        };

        (new Dispatcher($this->listener_provider))
            ->dispatch((new $event()))
            ->subscribe();
    }
}

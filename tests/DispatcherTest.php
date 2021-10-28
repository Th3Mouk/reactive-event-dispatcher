<?php

declare(strict_types=1);

namespace Th3Mouk\ReactiveEventDispatcher\Tests;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use Rx\Observable;
use Th3Mouk\ReactiveEventDispatcher\Dispatcher;
use Th3Mouk\ReactiveEventDispatcher\Listener;

class DispatcherTest extends TestCase
{
    protected function setUp(): void
    {
        $this->listener = new class implements Listener {
            public bool $hasBeenProcessed = false;

            public function process(object $event): Observable
            {
                return Observable::of('obs')
                    ->do(function (): void {
                        $this->hasBeenProcessed = true;
                    });
            }
        };

        $this->listenerProvider = new class ($this->listener) implements ListenerProviderInterface {
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
        $event = new class {
        };

        (new Dispatcher($this->listenerProvider))
            ->dispatch((new $event()))
            ->subscribe();

        $this->assertTrue($this->listener->hasBeenProcessed);
    }
}

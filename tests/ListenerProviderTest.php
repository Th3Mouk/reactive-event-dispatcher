<?php

declare(strict_types=1);

namespace Th3Mouk\ReactiveEventDispatcher\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Rx\Observable;
use Th3Mouk\ReactiveEventDispatcher\EventCorrelation;
use Th3Mouk\ReactiveEventDispatcher\Listener;
use Th3Mouk\ReactiveEventDispatcher\ListenerProvider;
use Th3Mouk\ReactiveEventDispatcher\Priority;

class ListenerProviderTest extends TestCase
{
    protected function setUp(): void
    {
        $this->empty_locator = new class implements ContainerInterface {
            /** @inheritDoc */
            public function get($id): void
            {
                throw new class implements NotFoundExceptionInterface {
                };
            }

            /** @inheritDoc */
            public function has($id)
            {
                return false;
            }
        };
    }

    public function testGetListenersForEventMustReturnEmptyIterable(): void
    {
        $listener_provider = new ListenerProvider($this->empty_locator, []);

        $event = new class {
        };

        $listeners = $listener_provider->getListenersForEvent($event);

        $this->assertCount(0, $listeners);
    }

    public function testGetListenersForEventSortCorrectly(): void
    {
        $locator = new class implements ContainerInterface {
            /** @inheritDoc */
            public function get($id)
            {
                if ($id === 'listener_7') {
                    return new class () {
                    };
                }

                return new class ($id) implements Listener {
                    public string $id;

                    public function __construct(string $id)
                    {
                        $this->id = $id;
                    }

                    public function process(object $event): Observable
                    {
                        return Observable::of(1);
                    }
                };
            }

            /** @inheritDoc */
            public function has($id)
            {
                return $id !== 'listener_6';
            }
        };

        $event = new class {
        };

        $listener_provider = new ListenerProvider(
            $locator,
            [
                EventCorrelation::create(get_class($event), 'listener_1', Priority::fromInt(0)),
                EventCorrelation::create(get_class($event), 'listener_2', Priority::fromInt(-64)),
                EventCorrelation::create(get_class($event), 'listener_3', Priority::fromInt(128)),
                EventCorrelation::create(get_class($event), 'listener_4', Priority::fromInt(32)),
                EventCorrelation::create(get_class($event), 'listener_5', Priority::fromInt(128)),
                EventCorrelation::create(get_class($event), 'listener_6', Priority::fromInt(128)),
                EventCorrelation::create(get_class($event), 'listener_7', Priority::fromInt(128)),
            ]
        );

        $listeners = $listener_provider->getListenersForEvent($event);

        $first = $listeners->current();

        $listeners->next();
        $second = $listeners->current();

        $listeners->next();
        $third = $listeners->current();

        $listeners->next();
        $fourth = $listeners->current();

        $listeners->next();
        $fifth = $listeners->current();

        $this->assertEquals('listener_3', $first[0]->id);
        $this->assertEquals('listener_5', $second[0]->id);
        $this->assertEquals('listener_4', $third[0]->id);
        $this->assertEquals('listener_1', $fourth[0]->id);
        $this->assertEquals('listener_2', $fifth[0]->id);
    }
}

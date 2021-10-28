<?php

declare(strict_types=1);

namespace Th3Mouk\ReactiveEventDispatcher;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Rx\Observable;

final class Dispatcher implements EventDispatcherInterface
{
    private ListenerProviderInterface $listenerProvider;

    public function __construct(ListenerProviderInterface $listenerProvider)
    {
        $this->listenerProvider = $listenerProvider;
    }

    /**
     * @return Observable
     *
     * @inheritDoc
     */
    public function dispatch(object $event)
    {
        $listeners = $this->listenerProvider->getListenersForEvent($event);

        $observable = Observable::of($event);

        foreach ($listeners as $listener) {
            \assert(\is_callable($listener));
            $observable = $observable
                ->flatMap($listener)
                ->mapTo($event);
        }

        return $observable;
    }
}

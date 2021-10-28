<?php

declare(strict_types=1);

namespace Th3Mouk\ReactiveEventDispatcher;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * @psalm-immutable
 */
final class ListenerProvider implements ListenerProviderInterface
{
    private ContainerInterface $serviceLocator;
    /** @psalm-var array<class-string, list<class-string>> */
    private array $sortedCorrelations;

    /**
     * @param array<EventCorrelation> $eventCorrelations
     */
    public function __construct(ContainerInterface $serviceLocator, array $eventCorrelations)
    {
        $this->serviceLocator     = $serviceLocator;
        $this->sortedCorrelations = $this->sortedEventCorrelation($eventCorrelations);
    }

    /**
     * @param array<EventCorrelation> $eventCorrelations
     *
     * @psalm-return array<class-string, list<class-string>>
     */
    private function sortedEventCorrelation(array $eventCorrelations): array
    {
        $sortedWithoutPriorities = array_reduce(
            $eventCorrelations,
            /**
             * @psalm-param array<class-string, array<int, list<class-string>>> $carry
             * @psalm-return array<class-string, array<int, list<class-string>>>
             */
            static function (array $carry, EventCorrelation $eventCorrelation): array {
                $carry[$eventCorrelation->eventFqcn][$eventCorrelation->priority->value][] = $eventCorrelation->listenerFqcn;

                return $carry;
            },
            []
        );

        /** @psalm-var array<class-string, list<class-string>> $fullySorted */
        $fullySorted = array_map(
            /**
             * @psalm-param array<int, list<class-string>> $eventListenersWithPriorities
             * @psalm-return list<class-string>
             */
            static function (array $eventListenersWithPriorities) {
                krsort($eventListenersWithPriorities);

                return array_reduce(
                    $eventListenersWithPriorities,
                    /**
                     * @psalm-param list<class-string> $carry
                     * @psalm-param list<class-string> $listenersSortedByPriorities
                     * @psalm-return list<class-string>
                     */
                    static function (array $carry, array $listenersSortedByPriorities): array {
                        return array_merge($carry, $listenersSortedByPriorities);
                    },
                    []
                );
            },
            $sortedWithoutPriorities
        );

        return $fullySorted;
    }

    /**
     * @return iterable<callable>
     *
     * @inheritDoc
     */
    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->orderedListenerFqcnIterableForEvent($event) as $listenerFqcn) {
            if (!$this->serviceLocator->has($listenerFqcn)) {
                continue;
            }

            $listener = $this->serviceLocator->get($listenerFqcn);

            if (!$listener instanceof Listener) {
                continue;
            }

            yield [$listener, 'process'];
        }
    }

    /**
     * @psalm-return array<class-string>
     */
    private function orderedListenerFqcnIterableForEvent(object $event): array
    {
        return $this->sortedCorrelations[get_class($event)] ?? [];
    }
}

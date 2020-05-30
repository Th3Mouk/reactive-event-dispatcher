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
    private ContainerInterface $service_locator;
    /** @psalm-var array<class-string, list<class-string>> */
    private array $sorted_correlations;

    /**
     * @param array<EventCorrelation> $event_correlations
     */
    public function __construct(ContainerInterface $service_locator, array $event_correlations)
    {
        $this->service_locator     = $service_locator;
        $this->sorted_correlations = $this->sortedEventCorrelation($event_correlations);
    }

    /**
     * @param array<EventCorrelation> $event_correlations
     *
     * @psalm-return array<class-string, list<class-string>>
     */
    private function sortedEventCorrelation(array $event_correlations): array
    {
        $sorted_without_priorities = array_reduce(
            $event_correlations,
            /**
             * @psalm-param array<class-string, array<int, list<class-string>>> $carry
             * @psalm-return array<class-string, array<int, list<class-string>>>
             */
            static function (array $carry, EventCorrelation $event_correlation): array {
                $carry[$event_correlation->event_fqcn][$event_correlation->priority->value][] = $event_correlation->listener_fqcn;

                return $carry;
            },
            []
        );

        /** @psalm-var array<class-string, list<class-string>> $fully_sorted */
        $fully_sorted = array_map(
            /**
             * @psalm-param array<int, list<class-string>> $event_listeners_with_priorities
             * @psalm-return list<class-string>
             */
            static function (array $event_listeners_with_priorities) {
                krsort($event_listeners_with_priorities);

                return array_reduce(
                    $event_listeners_with_priorities,
                    /**
                     * @psalm-param list<class-string> $carry
                     * @psalm-param list<class-string> $listeners_sorted_by_priorities
                     * @psalm-return list<class-string>
                     */
                    static function (array $carry, array $listeners_sorted_by_priorities): array {
                        return array_merge($carry, $listeners_sorted_by_priorities);
                    },
                    []
                );
            },
            $sorted_without_priorities
        );

        return $fully_sorted;
    }

    /**
     * @return iterable<callable>
     *
     * @inheritDoc
     */
    public function getListenersForEvent(object $event): iterable
    {
        if (!$event instanceof Event) {
            throw new \InvalidArgumentException();
        }

        foreach ($this->orderedListenerFqcnIterableForEvent($event) as $listener_fqcn) {
            if (!$this->service_locator->has($listener_fqcn)) {
                continue;
            }

            $listener = $this->service_locator->get($listener_fqcn);

            if (!$listener instanceof Listener) {
                continue;
            }

            yield [$listener, 'process'];
        }
    }

    /**
     * @psalm-return array<class-string>
     */
    private function orderedListenerFqcnIterableForEvent(Event $event): array
    {
        return $this->sorted_correlations[get_class($event)] ?? [];
    }
}

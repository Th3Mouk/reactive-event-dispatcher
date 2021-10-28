<?php

declare(strict_types=1);

namespace Th3Mouk\ReactiveEventDispatcher;

/**
 * @psalm-immutable
 */
final class EventCorrelation
{
    /** @psalm-var class-string */
    public string $eventFqcn;
    /** @psalm-var class-string */
    public string $listenerFqcn;
    public Priority $priority;

    /**
     * @psalm-param class-string $eventFqcn
     * @psalm-param class-string $listenerFqcn
     */
    private function __construct(
        string $eventFqcn,
        string $listenerFqcn,
        Priority $priority
    ) {
        $this->eventFqcn    = $eventFqcn;
        $this->listenerFqcn = $listenerFqcn;
        $this->priority     = $priority;
    }

    /**
     * @psalm-param class-string $eventFqcn
     * @psalm-param class-string $listenerFqcn
     */
    public static function create(
        string $eventFqcn,
        string $listenerFqcn,
        Priority $priority
    ): self {
        return new self($eventFqcn, $listenerFqcn, $priority);
    }
}

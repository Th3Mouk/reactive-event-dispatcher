<?php

declare(strict_types=1);

namespace Th3Mouk\ReactiveEventDispatcher;

/**
 * @psalm-immutable
 */
final class EventCorrelation
{
    /** @psalm-var class-string */
    public string $event_fqcn;
    /** @psalm-var class-string */
    public string $listener_fqcn;
    public Priority $priority;

    /**
     * @psalm-param class-string $event_fqcn
     * @psalm-param class-string $listener_fqcn
     */
    private function __construct(
        string $event_fqcn,
        string $listener_fqcn,
        Priority $priority
    ) {
        $this->event_fqcn    = $event_fqcn;
        $this->listener_fqcn = $listener_fqcn;
        $this->priority      = $priority;
    }

    /**
     * @psalm-param class-string $event_fqcn
     * @psalm-param class-string $listener_fqcn
     */
    public static function create(
        string $event_fqcn,
        string $listener_fqcn,
        Priority $priority
    ): self {
        return new self($event_fqcn, $listener_fqcn, $priority);
    }
}

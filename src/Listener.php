<?php

declare(strict_types=1);

namespace Th3Mouk\ReactiveEventDispatcher;

use Rx\Observable;

/**
 * @psalm-immutable
 */
interface Listener
{
    public function process(object $event): Observable;
}

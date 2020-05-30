Reactive Event Dispatcher // PSR-14
=================

This PHP library provide an immutable implementation of the PSR-14 for ReactiveX PHP.

[![Latest Stable Version](https://poser.pugx.org/th3mouk/reactive-event-dispatcher/v/stable)](https://packagist.org/packages/th3mouk/reactive-event-dispatcher)
[![Latest Unstable Version](https://poser.pugx.org/th3mouk/reactive-event-dispatcher/v/unstable)](https://packagist.org/packages/th3mouk/reactive-event-dispatcher)
[![Total Downloads](https://poser.pugx.org/th3mouk/reactive-event-dispatcher/downloads)](https://packagist.org/packages/th3mouk/reactive-event-dispatcher)
[![License](https://poser.pugx.org/th3mouk/reactive-event-dispatcher/license)](https://packagist.org/packages/th3mouk/reactive-event-dispatcher)

![](https://github.com/th3mouk/reactive-event-dispatcher/workflows/Continuous%20Integration/badge.svg)
[![Coverage Status](https://coveralls.io/repos/github/Th3Mouk/reactive-event-dispatcher/badge.svg?branch=master)](https://coveralls.io/github/Th3Mouk/reactive-event-dispatcher?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Th3Mouk/reactive-event-dispatcher/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Th3Mouk/reactive-event-dispatcher/?branch=master)

## Installation

`composer require th3mouk/reactive-event-dispatcher`

## Usage

[Psalm](https://psalm.dev/) usage is recommended. See relative 
[introduction](https://psalm.dev/articles/immutability-and-beyond) and 
[documentation](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-immutable).

```php
use Psr\Container\ContainerInterface;
use Rx\Observable;
use Th3Mouk\ReactiveEventDispatcher\Dispatcher;
use Th3Mouk\ReactiveEventDispatcher\Event;
use Th3Mouk\ReactiveEventDispatcher\EventCorrelation;
use Th3Mouk\ReactiveEventDispatcher\Listener;
use Th3Mouk\ReactiveEventDispatcher\ListenerProvider;
use Th3Mouk\ReactiveEventDispatcher\Priority;


$event = new class implements Event {};
$listener = new class implements Listener {
    public function process (Event $event) : Observable {
        return Observable::of(1);    
    }
};

// Link between an event and a listener
// Higher is the priority, earlier is the call
$event_correlations = [
    EventCorrelation::create(
        get_class($event),
        get_class($listener),
        Priority::fromInt(0),
    )
];

// Any object implementing ContainerInterface
// Listeners must be present into
$locator = new class implements ContainerInterface{
    public function get($id){
    }
    
    public function has($id){
    }
};


$listener_provider = new ListenerProvider($locator, $event_correlations);

$dispatcher = new Dispatcher($listener_provider);

$dispatcher->dispatch($event)->subscribe();
```

## Please

Feel free to improve this library.

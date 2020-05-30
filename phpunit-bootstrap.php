<?php

use Rx\Scheduler;

require_once 'vendor/autoload.php';

//You only need to set the default scheduler once
Scheduler::setDefaultFactory(function() {
    return new Scheduler\ImmediateScheduler();
});

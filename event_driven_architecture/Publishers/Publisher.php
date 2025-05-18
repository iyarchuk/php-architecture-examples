<?php

namespace EventDrivenArchitecture\Publishers;

use EventDrivenArchitecture\Events\Event;
use EventDrivenArchitecture\EventBus\EventBus;

/**
 * Publisher Interface
 * 
 * This interface defines the contract for all event publishers in the system.
 * Publishers are responsible for detecting events and publishing them to the event bus.
 */
interface Publisher
{
    /**
     * Set the event bus
     * 
     * @param EventBus $eventBus
     * @return void
     */
    public function setEventBus(EventBus $eventBus): void;
    
    /**
     * Publish an event to the event bus
     * 
     * @param Event $event
     * @return void
     */
    public function publish(Event $event): void;
}
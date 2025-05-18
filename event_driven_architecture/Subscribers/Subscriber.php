<?php

namespace EventDrivenArchitecture\Subscribers;

use EventDrivenArchitecture\Events\Event;

/**
 * Subscriber Interface
 * 
 * This interface defines the contract for all event subscribers in the system.
 * Subscribers are responsible for handling events they are interested in.
 */
interface Subscriber
{
    /**
     * Get the event types that this subscriber is interested in
     * 
     * @return array
     */
    public function getSubscribedEvents(): array;
    
    /**
     * Handle an event
     * 
     * @param Event $event
     * @return void
     */
    public function handle(Event $event): void;
    
    /**
     * Check if this subscriber can handle the given event
     * 
     * @param Event $event
     * @return bool
     */
    public function canHandle(Event $event): bool;
}
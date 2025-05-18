<?php

namespace EventSourcingArchitecture\Projections;

use EventSourcingArchitecture\Events\Event;

/**
 * Projection Interface
 * 
 * This interface defines the contract for projections (read models) in the system.
 * Projections are responsible for transforming the event stream into a format that is optimized for querying.
 */
interface Projection
{
    /**
     * Project an event
     * 
     * @param Event $event The event to project
     * @return void
     */
    public function project(Event $event): void;
    
    /**
     * Reset the projection
     * 
     * @return void
     */
    public function reset(): void;
    
    /**
     * Get the name of the projection
     * 
     * @return string
     */
    public function getName(): string;
    
    /**
     * Get the event types that this projection is interested in
     * 
     * @return array
     */
    public function getEventTypes(): array;
    
    /**
     * Check if this projection can handle the given event
     * 
     * @param Event $event The event to check
     * @return bool
     */
    public function canHandle(Event $event): bool;
}
<?php

namespace EventSourcingArchitecture\EventStore;

use EventSourcingArchitecture\Events\Event;

/**
 * EventStore Interface
 * 
 * This interface defines the contract for storing and retrieving events.
 */
interface EventStore
{
    /**
     * Append events to an aggregate's event stream
     * 
     * @param string $aggregateId The ID of the aggregate
     * @param array $events The events to append
     * @param int $expectedVersion The expected version of the aggregate
     * @throws \RuntimeException If there is a concurrency conflict
     * @return void
     */
    public function appendToStream(string $aggregateId, array $events, int $expectedVersion): void;
    
    /**
     * Get events for a specific aggregate
     * 
     * @param string $aggregateId The ID of the aggregate
     * @return array
     */
    public function getEventsForAggregate(string $aggregateId): array;
    
    /**
     * Get all events
     * 
     * @return array
     */
    public function getAllEvents(): array;
    
    /**
     * Get events of a specific type
     * 
     * @param string $eventType The type of events to get
     * @return array
     */
    public function getEventsByType(string $eventType): array;
    
    /**
     * Get events that occurred after a specific date
     * 
     * @param \DateTimeInterface $date The date
     * @return array
     */
    public function getEventsAfterDate(\DateTimeInterface $date): array;
    
    /**
     * Get the current version of an aggregate
     * 
     * @param string $aggregateId The ID of the aggregate
     * @return int
     */
    public function getCurrentVersion(string $aggregateId): int;
    
    /**
     * Check if an aggregate exists
     * 
     * @param string $aggregateId The ID of the aggregate
     * @return bool
     */
    public function aggregateExists(string $aggregateId): bool;
}
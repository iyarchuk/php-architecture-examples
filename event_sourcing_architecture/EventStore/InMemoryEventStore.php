<?php

namespace EventSourcingArchitecture\EventStore;

use EventSourcingArchitecture\Events\Event;

/**
 * InMemoryEventStore
 * 
 * This class provides an in-memory implementation of the EventStore interface.
 * It is intended for demonstration purposes only and should not be used in production.
 */
class InMemoryEventStore implements EventStore
{
    /**
     * @var array The events stored in memory, indexed by aggregate ID
     */
    private array $events = [];
    
    /**
     * @var array All events in chronological order
     */
    private array $allEvents = [];
    
    /**
     * Append events to an aggregate's event stream
     * 
     * @param string $aggregateId The ID of the aggregate
     * @param array $events The events to append
     * @param int $expectedVersion The expected version of the aggregate
     * @throws \RuntimeException If there is a concurrency conflict
     * @return void
     */
    public function appendToStream(string $aggregateId, array $events, int $expectedVersion): void
    {
        // Check for concurrency conflicts
        $currentVersion = $this->getCurrentVersion($aggregateId);
        if ($currentVersion !== $expectedVersion) {
            throw new \RuntimeException(
                "Concurrency conflict: expected version $expectedVersion but got $currentVersion"
            );
        }
        
        // Initialize the event stream for this aggregate if it doesn't exist
        if (!isset($this->events[$aggregateId])) {
            $this->events[$aggregateId] = [];
        }
        
        // Append the events to the aggregate's event stream
        foreach ($events as $event) {
            if (!$event instanceof Event) {
                throw new \InvalidArgumentException('Event must be an instance of Event');
            }
            
            $this->events[$aggregateId][] = $event;
            $this->allEvents[] = $event;
        }
    }
    
    /**
     * Get events for a specific aggregate
     * 
     * @param string $aggregateId The ID of the aggregate
     * @return array
     */
    public function getEventsForAggregate(string $aggregateId): array
    {
        return $this->events[$aggregateId] ?? [];
    }
    
    /**
     * Get all events
     * 
     * @return array
     */
    public function getAllEvents(): array
    {
        return $this->allEvents;
    }
    
    /**
     * Get events of a specific type
     * 
     * @param string $eventType The type of events to get
     * @return array
     */
    public function getEventsByType(string $eventType): array
    {
        return array_filter($this->allEvents, function (Event $event) use ($eventType) {
            return $event->getEventType() === $eventType;
        });
    }
    
    /**
     * Get events that occurred after a specific date
     * 
     * @param \DateTimeInterface $date The date
     * @return array
     */
    public function getEventsAfterDate(\DateTimeInterface $date): array
    {
        return array_filter($this->allEvents, function (Event $event) use ($date) {
            return $event->getOccurredAt() > $date;
        });
    }
    
    /**
     * Get the current version of an aggregate
     * 
     * @param string $aggregateId The ID of the aggregate
     * @return int
     */
    public function getCurrentVersion(string $aggregateId): int
    {
        if (!isset($this->events[$aggregateId]) || empty($this->events[$aggregateId])) {
            return 0;
        }
        
        $events = $this->events[$aggregateId];
        $lastEvent = end($events);
        
        return $lastEvent->getAggregateVersion();
    }
    
    /**
     * Check if an aggregate exists
     * 
     * @param string $aggregateId The ID of the aggregate
     * @return bool
     */
    public function aggregateExists(string $aggregateId): bool
    {
        return isset($this->events[$aggregateId]) && !empty($this->events[$aggregateId]);
    }
    
    /**
     * Clear all events (for testing purposes)
     * 
     * @return void
     */
    public function clear(): void
    {
        $this->events = [];
        $this->allEvents = [];
    }
}
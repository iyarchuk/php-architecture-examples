<?php

namespace EventSourcingArchitecture\Aggregates;

use EventSourcingArchitecture\Events\Event;

/**
 * Aggregate
 * 
 * This abstract class represents the base class for all aggregates in the system.
 * Aggregates are domain objects that encapsulate and protect business invariants.
 */
abstract class Aggregate
{
    /**
     * @var string The unique identifier of the aggregate
     */
    protected string $id;
    
    /**
     * @var int The current version of the aggregate
     */
    protected int $version = 0;
    
    /**
     * @var array The events that have been applied to this aggregate but not yet committed
     */
    private array $uncommittedEvents = [];
    
    /**
     * Constructor
     * 
     * @param string $id The unique identifier of the aggregate
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }
    
    /**
     * Get the unique identifier of the aggregate
     * 
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
    
    /**
     * Get the current version of the aggregate
     * 
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }
    
    /**
     * Apply an event to the aggregate
     * 
     * @param Event $event The event to apply
     * @param bool $isNew Whether this is a new event or a historical event being replayed
     * @return void
     */
    protected function apply(Event $event, bool $isNew = true): void
    {
        // Apply the event to the aggregate
        $this->applyEvent($event);
        
        // Increment the version
        $this->version++;
        
        // If this is a new event, add it to the uncommitted events
        if ($isNew) {
            $this->uncommittedEvents[] = $event;
        }
    }
    
    /**
     * Apply an event to the aggregate
     * This method should be implemented by subclasses to handle specific event types
     * 
     * @param Event $event The event to apply
     * @return void
     */
    abstract protected function applyEvent(Event $event): void;
    
    /**
     * Get the uncommitted events
     * 
     * @return array
     */
    public function getUncommittedEvents(): array
    {
        return $this->uncommittedEvents;
    }
    
    /**
     * Clear the uncommitted events
     * 
     * @return void
     */
    public function clearUncommittedEvents(): void
    {
        $this->uncommittedEvents = [];
    }
    
    /**
     * Load the aggregate from a stream of events
     * 
     * @param array $events The events to load
     * @return static
     */
    public static function fromEvents(string $id, array $events): self
    {
        $aggregate = new static($id);
        
        foreach ($events as $event) {
            $aggregate->apply($event, false);
        }
        
        return $aggregate;
    }
    
    /**
     * Generate a unique aggregate ID
     * 
     * @return string
     */
    public static function generateId(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
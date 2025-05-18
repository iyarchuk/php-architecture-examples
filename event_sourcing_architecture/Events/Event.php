<?php

namespace EventSourcingArchitecture\Events;

/**
 * Event
 * 
 * This abstract class represents the base class for all events in the system.
 * Events are immutable objects that represent something that has happened.
 */
abstract class Event
{
    /**
     * @var string The unique identifier of the event
     */
    private string $eventId;
    
    /**
     * @var string The type of the event
     */
    private string $eventType;
    
    /**
     * @var string The ID of the aggregate this event belongs to
     */
    private string $aggregateId;
    
    /**
     * @var int The version of the aggregate after this event is applied
     */
    private int $aggregateVersion;
    
    /**
     * @var \DateTimeImmutable The timestamp when the event occurred
     */
    private \DateTimeImmutable $occurredAt;
    
    /**
     * @var array The event data
     */
    private array $data;
    
    /**
     * Constructor
     * 
     * @param string $aggregateId The ID of the aggregate this event belongs to
     * @param int $aggregateVersion The version of the aggregate after this event is applied
     * @param array $data The event data
     */
    public function __construct(string $aggregateId, int $aggregateVersion, array $data = [])
    {
        $this->eventId = $this->generateEventId();
        $this->eventType = $this->getEventType();
        $this->aggregateId = $aggregateId;
        $this->aggregateVersion = $aggregateVersion;
        $this->occurredAt = new \DateTimeImmutable();
        $this->data = $data;
    }
    
    /**
     * Get the unique identifier of the event
     * 
     * @return string
     */
    public function getEventId(): string
    {
        return $this->eventId;
    }
    
    /**
     * Get the type of the event
     * 
     * @return string
     */
    public function getEventType(): string
    {
        if (isset($this->eventType)) {
            return $this->eventType;
        }
        
        // Derive the event type from the class name
        $className = get_class($this);
        $parts = explode('\\', $className);
        $shortName = end($parts);
        
        // Convert from CamelCase to snake_case
        $snakeCase = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $shortName));
        
        return $snakeCase;
    }
    
    /**
     * Get the ID of the aggregate this event belongs to
     * 
     * @return string
     */
    public function getAggregateId(): string
    {
        return $this->aggregateId;
    }
    
    /**
     * Get the version of the aggregate after this event is applied
     * 
     * @return int
     */
    public function getAggregateVersion(): int
    {
        return $this->aggregateVersion;
    }
    
    /**
     * Get the timestamp when the event occurred
     * 
     * @return \DateTimeImmutable
     */
    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
    
    /**
     * Get the event data
     * 
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
    
    /**
     * Generate a unique event ID
     * 
     * @return string
     */
    private function generateEventId(): string
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
    
    /**
     * Convert the event to an array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'event_id' => $this->eventId,
            'event_type' => $this->eventType,
            'aggregate_id' => $this->aggregateId,
            'aggregate_version' => $this->aggregateVersion,
            'occurred_at' => $this->occurredAt->format('Y-m-d\TH:i:s.uP'),
            'data' => $this->data
        ];
    }
    
    /**
     * Create an event from an array
     * 
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): self
    {
        $event = new static(
            $data['aggregate_id'],
            $data['aggregate_version'],
            $data['data'] ?? []
        );
        
        // Set the event ID and timestamp from the stored data
        $reflectionClass = new \ReflectionClass($event);
        
        $eventIdProperty = $reflectionClass->getProperty('eventId');
        $eventIdProperty->setAccessible(true);
        $eventIdProperty->setValue($event, $data['event_id']);
        
        $occurredAtProperty = $reflectionClass->getProperty('occurredAt');
        $occurredAtProperty->setAccessible(true);
        $occurredAtProperty->setValue($event, new \DateTimeImmutable($data['occurred_at']));
        
        return $event;
    }
}
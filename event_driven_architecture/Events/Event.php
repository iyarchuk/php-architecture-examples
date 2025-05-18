<?php

namespace EventDrivenArchitecture\Events;

/**
 * Event
 * 
 * This abstract class represents the base class for all events in the system.
 * Events are immutable objects that represent something that has happened.
 */
abstract class Event
{
    /**
     * @var string The name/type of the event
     */
    protected string $name;
    
    /**
     * @var \DateTimeImmutable The timestamp when the event occurred
     */
    protected \DateTimeImmutable $timestamp;
    
    /**
     * Constructor
     * 
     * @param string $name The name/type of the event
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->timestamp = new \DateTimeImmutable();
    }
    
    /**
     * Get the name/type of the event
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Get the timestamp when the event occurred
     * 
     * @return \DateTimeImmutable
     */
    public function getTimestamp(): \DateTimeImmutable
    {
        return $this->timestamp;
    }
    
    /**
     * Get the data associated with the event
     * 
     * @return array
     */
    abstract public function getData(): array;
    
    /**
     * Convert the event to a string representation
     * 
     * @return string
     */
    public function toString(): string
    {
        return sprintf(
            '[%s] %s: %s',
            $this->timestamp->format('Y-m-d H:i:s'),
            $this->name,
            json_encode($this->getData())
        );
    }
    
    /**
     * Convert the event to a string
     * 
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
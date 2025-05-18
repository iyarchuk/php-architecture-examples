<?php

namespace CQRSArchitecture\EventStore;

/**
 * Event
 * 
 * This abstract class represents the base class for all events.
 * Events represent state changes in the system.
 */
abstract class Event
{
    /**
     * @var string The type of the event
     */
    protected string $type;
    
    /**
     * @var string The timestamp when the event occurred
     */
    protected string $timestamp;
    
    /**
     * Constructor
     * 
     * @param string $type The type of the event
     */
    public function __construct(string $type)
    {
        $this->type = $type;
        $this->timestamp = date('Y-m-d H:i:s');
    }
    
    /**
     * Get the type of the event
     * 
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
    
    /**
     * Get the timestamp when the event occurred
     * 
     * @return string
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }
    
    /**
     * Get the data of the event
     * 
     * @return array
     */
    abstract public function getData(): array;
    
    /**
     * Convert the event to an array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'timestamp' => $this->timestamp,
            'data' => $this->getData()
        ];
    }
}
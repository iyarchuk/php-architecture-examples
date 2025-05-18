<?php

namespace ReactiveArchitecture\Core;

/**
 * EventSubscriber implements the Subscriber part of the Publisher-Subscriber pattern.
 * It receives and processes events from publishers.
 */
interface EventSubscriber
{
    /**
     * Receive notification of an event
     * 
     * @param string $eventType Type of event
     * @param mixed $eventData Event data
     * @return void
     */
    public function notify(string $eventType, $eventData): void;
}

/**
 * AbstractEventSubscriber provides a base implementation of EventSubscriber
 * with common functionality for concrete subscribers.
 */
abstract class AbstractEventSubscriber implements EventSubscriber
{
    /** @var callable[] Event handlers by event type */
    protected $handlers = [];
    
    /** @var bool Whether the subscriber is active */
    protected $active = true;
    
    /**
     * Register a handler for a specific event type
     * 
     * @param string $eventType Type of event to handle
     * @param callable $handler Function to handle the event
     * @return self
     */
    public function on(string $eventType, callable $handler): self
    {
        if (!isset($this->handlers[$eventType])) {
            $this->handlers[$eventType] = [];
        }
        
        $this->handlers[$eventType][] = $handler;
        
        return $this;
    }
    
    /**
     * Pause event processing
     * 
     * @return self
     */
    public function pause(): self
    {
        $this->active = false;
        return $this;
    }
    
    /**
     * Resume event processing
     * 
     * @return self
     */
    public function resume(): self
    {
        $this->active = true;
        return $this;
    }
    
    /**
     * Check if subscriber is active
     * 
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }
    
    /**
     * {@inheritdoc}
     */
    public function notify(string $eventType, $eventData): void
    {
        // Skip processing if subscriber is paused
        if (!$this->active) {
            return;
        }
        
        // If no handlers for this event type, use default handler
        if (!isset($this->handlers[$eventType])) {
            $this->handleUnknownEvent($eventType, $eventData);
            return;
        }
        
        // Call all handlers for this event type
        foreach ($this->handlers[$eventType] as $handler) {
            call_user_func($handler, $eventData);
        }
    }
    
    /**
     * Handle unknown event types
     * 
     * @param string $eventType Type of event
     * @param mixed $eventData Event data
     * @return void
     */
    protected function handleUnknownEvent(string $eventType, $eventData): void
    {
        // Default implementation does nothing
        // Subclasses can override this method to handle unknown events
    }
}

/**
 * SimpleEventSubscriber is a concrete implementation of EventSubscriber
 * that can be used directly without subclassing.
 */
class SimpleEventSubscriber extends AbstractEventSubscriber
{
    /** @var callable Default handler for unknown events */
    private $defaultHandler;
    
    /**
     * Set default handler for unknown events
     * 
     * @param callable $handler Function to handle unknown events
     * @return self
     */
    public function setDefaultHandler(callable $handler): self
    {
        $this->defaultHandler = $handler;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function handleUnknownEvent(string $eventType, $eventData): void
    {
        if ($this->defaultHandler !== null) {
            call_user_func($this->defaultHandler, $eventType, $eventData);
        }
    }
}
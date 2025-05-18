<?php

namespace EventDrivenArchitecture\Subscribers;

use EventDrivenArchitecture\Events\Event;
use EventDrivenArchitecture\Handlers\LoggingHandler;

/**
 * Logger
 * 
 * This class is responsible for logging events.
 */
class Logger implements Subscriber
{
    /**
     * @var LoggingHandler The logging handler
     */
    private LoggingHandler $handler;
    
    /**
     * Constructor
     * 
     * @param LoggingHandler $handler
     */
    public function __construct(LoggingHandler $handler)
    {
        $this->handler = $handler;
    }
    
    /**
     * Get the event types that this subscriber is interested in
     * 
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        // Logger is interested in all events
        return ['*'];
    }
    
    /**
     * Handle an event
     * 
     * @param Event $event
     * @return void
     */
    public function handle(Event $event): void
    {
        $this->handler->logEvent(
            $event->getName(),
            $event->getTimestamp(),
            $event->getData()
        );
    }
    
    /**
     * Check if this subscriber can handle the given event
     * 
     * @param Event $event
     * @return bool
     */
    public function canHandle(Event $event): bool
    {
        // Logger can handle all events
        return true;
    }
}
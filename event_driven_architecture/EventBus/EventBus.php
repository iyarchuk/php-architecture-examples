<?php

namespace EventDrivenArchitecture\EventBus;

use EventDrivenArchitecture\Events\Event;
use EventDrivenArchitecture\Subscribers\Subscriber;

/**
 * EventBus
 * 
 * This class is responsible for dispatching events to subscribers.
 * It maintains a registry of subscribers and their interests.
 */
class EventBus
{
    /**
     * @var array The subscribers registered with this event bus
     */
    private array $subscribers = [];
    
    /**
     * @var array The event history
     */
    private array $eventHistory = [];
    
    /**
     * Register a subscriber
     * 
     * @param Subscriber $subscriber
     * @return void
     */
    public function register(Subscriber $subscriber): void
    {
        $this->subscribers[] = $subscriber;
    }
    
    /**
     * Unregister a subscriber
     * 
     * @param Subscriber $subscriber
     * @return void
     */
    public function unregister(Subscriber $subscriber): void
    {
        $key = array_search($subscriber, $this->subscribers, true);
        if ($key !== false) {
            unset($this->subscribers[$key]);
            $this->subscribers = array_values($this->subscribers); // Reindex array
        }
    }
    
    /**
     * Dispatch an event to all interested subscribers
     * 
     * @param Event $event
     * @return void
     */
    public function dispatch(Event $event): void
    {
        // Add event to history
        $this->eventHistory[] = [
            'event' => $event,
            'timestamp' => new \DateTimeImmutable(),
        ];
        
        // Dispatch event to interested subscribers
        foreach ($this->subscribers as $subscriber) {
            if ($subscriber->canHandle($event)) {
                $subscriber->handle($event);
            }
        }
    }
    
    /**
     * Get all subscribers
     * 
     * @return array
     */
    public function getSubscribers(): array
    {
        return $this->subscribers;
    }
    
    /**
     * Get subscribers interested in a specific event type
     * 
     * @param string $eventType
     * @return array
     */
    public function getSubscribersForEventType(string $eventType): array
    {
        $interestedSubscribers = [];
        
        foreach ($this->subscribers as $subscriber) {
            $subscribedEvents = $subscriber->getSubscribedEvents();
            
            if (in_array($eventType, $subscribedEvents) || in_array('*', $subscribedEvents)) {
                $interestedSubscribers[] = $subscriber;
            }
        }
        
        return $interestedSubscribers;
    }
    
    /**
     * Get the event history
     * 
     * @return array
     */
    public function getEventHistory(): array
    {
        return $this->eventHistory;
    }
    
    /**
     * Clear the event history
     * 
     * @return void
     */
    public function clearEventHistory(): void
    {
        $this->eventHistory = [];
    }
}
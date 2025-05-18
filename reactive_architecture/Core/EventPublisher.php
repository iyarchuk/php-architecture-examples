<?php

namespace ReactiveArchitecture\Core;

/**
 * EventPublisher implements the Publisher part of the Publisher-Subscriber pattern.
 * It allows publishing events to multiple subscribers asynchronously.
 */
class EventPublisher
{
    /** @var array Subscribers categorized by event type */
    private $subscribers = [];

    /** @var bool Whether to buffer events */
    private $bufferEvents;

    /** @var array Buffered events */
    private $eventBuffer = [];

    /** @var int Maximum buffer size */
    private $maxBufferSize;

    /**
     * Constructor
     * 
     * @param bool $bufferEvents Whether to buffer events
     * @param int $maxBufferSize Maximum buffer size
     */
    public function __construct(bool $bufferEvents = false, int $maxBufferSize = 1000)
    {
        $this->bufferEvents = $bufferEvents;
        $this->maxBufferSize = $maxBufferSize;
    }

    /**
     * Register a subscriber for a specific event type
     * 
     * @param string $eventType Type of event to subscribe to
     * @param \ReactiveArchitecture\Core\EventSubscriber $subscriber Subscriber object
     * @return void
     */
    public function subscribe(string $eventType, \ReactiveArchitecture\Core\EventSubscriber $subscriber): void
    {
        if (!isset($this->subscribers[$eventType])) {
            $this->subscribers[$eventType] = [];
        }

        $this->subscribers[$eventType][] = $subscriber;
    }

    /**
     * Unregister a subscriber for a specific event type
     * 
     * @param string $eventType Type of event
     * @param \ReactiveArchitecture\Core\EventSubscriber $subscriber Subscriber object
     * @return bool Success
     */
    public function unsubscribe(string $eventType, \ReactiveArchitecture\Core\EventSubscriber $subscriber): bool
    {
        if (!isset($this->subscribers[$eventType])) {
            return false;
        }

        $key = array_search($subscriber, $this->subscribers[$eventType], true);

        if ($key !== false) {
            unset($this->subscribers[$eventType][$key]);
            return true;
        }

        return false;
    }

    /**
     * Publish an event to all subscribers
     * 
     * @param string $eventType Type of event
     * @param mixed $eventData Event data
     * @return void
     */
    public function publish(string $eventType, $eventData): void
    {
        // Buffer the event if buffering is enabled
        if ($this->bufferEvents) {
            $this->bufferEvent($eventType, $eventData);
            return;
        }

        $this->dispatchEvent($eventType, $eventData);
    }

    /**
     * Buffer an event for later dispatch
     * 
     * @param string $eventType Type of event
     * @param mixed $eventData Event data
     * @return void
     */
    private function bufferEvent(string $eventType, $eventData): void
    {
        $this->eventBuffer[] = [
            'type' => $eventType,
            'data' => $eventData
        ];

        // If buffer exceeds max size, flush oldest events
        if (count($this->eventBuffer) > $this->maxBufferSize) {
            $this->flushBuffer(ceil($this->maxBufferSize / 2));
        }
    }

    /**
     * Flush buffered events
     * 
     * @param int|null $count Number of events to flush (null for all)
     * @return void
     */
    public function flushBuffer(?int $count = null): void
    {
        $eventsToFlush = $count === null ? $this->eventBuffer : array_slice($this->eventBuffer, 0, $count);

        foreach ($eventsToFlush as $key => $event) {
            $this->dispatchEvent($event['type'], $event['data']);
            unset($this->eventBuffer[$key]);
        }

        // Reindex array after unsetting elements
        $this->eventBuffer = array_values($this->eventBuffer);
    }

    /**
     * Dispatch an event to all subscribers
     * 
     * @param string $eventType Type of event
     * @param mixed $eventData Event data
     * @return void
     */
    private function dispatchEvent(string $eventType, $eventData): void
    {
        // If no subscribers for this event type, do nothing
        if (!isset($this->subscribers[$eventType])) {
            return;
        }

        // Notify all subscribers
        foreach ($this->subscribers[$eventType] as $subscriber) {
            $subscriber->notify($eventType, $eventData);
        }
    }
}

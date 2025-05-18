<?php

namespace ReactiveArchitecture\Core;

/**
 * EventStream represents a stream of events that can be subscribed to and processed asynchronously.
 * It implements the Iterator pattern to allow sequential access to events.
 */
class EventStream implements \Iterator
{
    /** @var array The events in the stream */
    private $events = [];
    
    /** @var int Current position in the stream */
    private $position = 0;
    
    /** @var array Subscribers to this stream */
    private $subscribers = [];
    
    /**
     * Add an event to the stream
     * 
     * @param mixed $event The event to add
     * @return void
     */
    public function push($event): void
    {
        $this->events[] = $event;
        $this->notifySubscribers($event);
    }
    
    /**
     * Subscribe to events in this stream
     * 
     * @param callable $callback Function to call when an event is pushed
     * @return int Subscription ID
     */
    public function subscribe(callable $callback): int
    {
        $id = count($this->subscribers);
        $this->subscribers[$id] = $callback;
        return $id;
    }
    
    /**
     * Unsubscribe from events
     * 
     * @param int $id Subscription ID
     * @return bool Success
     */
    public function unsubscribe(int $id): bool
    {
        if (isset($this->subscribers[$id])) {
            unset($this->subscribers[$id]);
            return true;
        }
        return false;
    }
    
    /**
     * Notify all subscribers of a new event
     * 
     * @param mixed $event The event
     * @return void
     */
    private function notifySubscribers($event): void
    {
        foreach ($this->subscribers as $callback) {
            call_user_func($callback, $event);
        }
    }
    
    /**
     * Apply a transformation to each event in the stream
     * 
     * @param callable $transformer Function to transform events
     * @return EventStream New stream with transformed events
     */
    public function map(callable $transformer): EventStream
    {
        $newStream = new EventStream();
        foreach ($this->events as $event) {
            $newStream->push($transformer($event));
        }
        return $newStream;
    }
    
    /**
     * Filter events in the stream
     * 
     * @param callable $predicate Function to test events
     * @return EventStream New stream with filtered events
     */
    public function filter(callable $predicate): EventStream
    {
        $newStream = new EventStream();
        foreach ($this->events as $event) {
            if ($predicate($event)) {
                $newStream->push($event);
            }
        }
        return $newStream;
    }
    
    // Iterator implementation
    
    public function current()
    {
        return $this->events[$this->position];
    }
    
    public function key()
    {
        return $this->position;
    }
    
    public function next()
    {
        ++$this->position;
    }
    
    public function rewind()
    {
        $this->position = 0;
    }
    
    public function valid()
    {
        return isset($this->events[$this->position]);
    }
}
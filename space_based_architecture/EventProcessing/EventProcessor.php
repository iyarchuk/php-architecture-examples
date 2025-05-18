<?php

namespace SpaceBasedArchitecture\EventProcessing;

use SpaceBasedArchitecture\Space\SpaceInterface;

/**
 * Implementation of Event Processor for the space-based architecture example.
 * Handles events that occur in the space, such as data updates or removals.
 */
class EventProcessor implements EventProcessorInterface
{
    /**
     * The space to listen for events on.
     *
     * @var SpaceInterface
     */
    protected $space;

    /**
     * The registered event handlers.
     *
     * @var array
     */
    protected $handlers = [];

    /**
     * The status of the event processor.
     *
     * @var string
     */
    protected $status = 'stopped';

    /**
     * Initialize the event processor.
     *
     * @param SpaceInterface $space The space to listen for events on
     * @return void
     */
    public function initialize(SpaceInterface $space): void
    {
        $this->space = $space;
        $this->status = 'initialized';
    }

    /**
     * Register an event handler.
     *
     * @param string $event The event to handle
     * @param callable $handler The handler function
     * @return void
     */
    public function registerHandler(string $event, callable $handler): void
    {
        if (!isset($this->handlers[$event])) {
            $this->handlers[$event] = [];
        }
        
        $this->handlers[$event][] = $handler;
    }

    /**
     * Process an event.
     *
     * @param string $event The event to process
     * @param array $data The data associated with the event
     * @return void
     */
    public function processEvent(string $event, array $data): void
    {
        if (!isset($this->handlers[$event])) {
            return;
        }
        
        foreach ($this->handlers[$event] as $handler) {
            call_user_func($handler, $data);
        }
    }

    /**
     * Start the event processor.
     * This method sets up event listeners and starts processing events.
     *
     * @return void
     */
    public function start(): void
    {
        if ($this->status === 'running') {
            return;
        }
        
        // Register event listeners with the space
        $this->space->on('write', function ($data) {
            $this->processEvent('write', $data);
        });
        
        $this->space->on('take', function ($data) {
            $this->processEvent('take', $data);
        });
        
        $this->status = 'running';
    }

    /**
     * Stop the event processor.
     * This method cleans up resources and stops processing events.
     *
     * @return void
     */
    public function stop(): void
    {
        // In a real implementation, we would unregister event listeners here
        // For this example, we'll just update the status
        $this->status = 'stopped';
    }

    /**
     * Get the status of the event processor.
     *
     * @return string The status of the event processor
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Get all registered event handlers.
     *
     * @return array An array of event handlers
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * Get the handlers for a specific event.
     *
     * @param string $event The event to get handlers for
     * @return array An array of handlers for the event
     */
    public function getHandlersForEvent(string $event): array
    {
        return $this->handlers[$event] ?? [];
    }

    /**
     * Clear all event handlers.
     *
     * @return void
     */
    public function clearHandlers(): void
    {
        $this->handlers = [];
    }

    /**
     * Clear the handlers for a specific event.
     *
     * @param string $event The event to clear handlers for
     * @return void
     */
    public function clearHandlersForEvent(string $event): void
    {
        if (isset($this->handlers[$event])) {
            $this->handlers[$event] = [];
        }
    }
}
<?php

namespace SpaceBasedArchitecture\EventProcessing;

use SpaceBasedArchitecture\Space\SpaceInterface;

/**
 * Interface for Event Processors in a Space-Based Architecture.
 * Event Processors handle events that occur in the space, such as data updates or removals.
 */
interface EventProcessorInterface
{
    /**
     * Initialize the event processor.
     *
     * @param SpaceInterface $space The space to listen for events on
     * @return void
     */
    public function initialize(SpaceInterface $space): void;

    /**
     * Register an event handler.
     *
     * @param string $event The event to handle
     * @param callable $handler The handler function
     * @return void
     */
    public function registerHandler(string $event, callable $handler): void;

    /**
     * Process an event.
     *
     * @param string $event The event to process
     * @param array $data The data associated with the event
     * @return void
     */
    public function processEvent(string $event, array $data): void;

    /**
     * Start the event processor.
     * This method should set up event listeners and start processing events.
     *
     * @return void
     */
    public function start(): void;

    /**
     * Stop the event processor.
     * This method should clean up resources and stop processing events.
     *
     * @return void
     */
    public function stop(): void;

    /**
     * Get the status of the event processor.
     *
     * @return string The status of the event processor
     */
    public function getStatus(): string;
}
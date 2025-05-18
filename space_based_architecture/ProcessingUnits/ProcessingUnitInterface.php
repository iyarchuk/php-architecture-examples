<?php

namespace SpaceBasedArchitecture\ProcessingUnits;

use SpaceBasedArchitecture\Space\SpaceInterface;

/**
 * Interface for Processing Units in a Space-Based Architecture.
 * A Processing Unit is a self-contained application module that includes
 * both the business logic and the data it needs to operate.
 */
interface ProcessingUnitInterface
{
    /**
     * Initialize the processing unit.
     *
     * @param SpaceInterface $space The space to use for communication
     * @return void
     */
    public function initialize(SpaceInterface $space): void;

    /**
     * Process incoming data.
     *
     * @param mixed $data The data to process
     * @return mixed The result of the processing
     */
    public function process($data);

    /**
     * Start the processing unit.
     * This method should set up event listeners and start processing data.
     *
     * @return void
     */
    public function start(): void;

    /**
     * Stop the processing unit.
     * This method should clean up resources and stop processing data.
     *
     * @return void
     */
    public function stop(): void;

    /**
     * Get the name of the processing unit.
     *
     * @return string The name of the processing unit
     */
    public function getName(): string;

    /**
     * Get the status of the processing unit.
     *
     * @return string The status of the processing unit (e.g., 'running', 'stopped')
     */
    public function getStatus(): string;
}
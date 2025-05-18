<?php

namespace SpaceBasedArchitecture\ProcessingUnits;

use SpaceBasedArchitecture\Space\SpaceInterface;

/**
 * Abstract base class for Processing Units in a Space-Based Architecture.
 * Provides common functionality for all processing units.
 */
abstract class ProcessingUnit implements ProcessingUnitInterface
{
    /**
     * The space used for communication.
     *
     * @var SpaceInterface
     */
    protected $space;

    /**
     * The name of the processing unit.
     *
     * @var string
     */
    protected $name;

    /**
     * The status of the processing unit.
     *
     * @var string
     */
    protected $status = 'stopped';

    /**
     * Constructor.
     *
     * @param string $name The name of the processing unit
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Initialize the processing unit.
     *
     * @param SpaceInterface $space The space to use for communication
     * @return void
     */
    public function initialize(SpaceInterface $space): void
    {
        $this->space = $space;
    }

    /**
     * Start the processing unit.
     * This method should be overridden by subclasses to set up event listeners.
     *
     * @return void
     */
    public function start(): void
    {
        $this->status = 'running';
    }

    /**
     * Stop the processing unit.
     * This method should be overridden by subclasses to clean up resources.
     *
     * @return void
     */
    public function stop(): void
    {
        $this->status = 'stopped';
    }

    /**
     * Get the name of the processing unit.
     *
     * @return string The name of the processing unit
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the status of the processing unit.
     *
     * @return string The status of the processing unit
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Process incoming data.
     * This method must be implemented by subclasses.
     *
     * @param mixed $data The data to process
     * @return mixed The result of the processing
     */
    abstract public function process($data);
}
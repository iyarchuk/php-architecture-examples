<?php

namespace SpaceBasedArchitecture\Middleware;

use SpaceBasedArchitecture\Space\SpaceInterface;
use SpaceBasedArchitecture\ProcessingUnits\ProcessingUnitInterface;

/**
 * Interface for Space-Based Middleware in a Space-Based Architecture.
 * The middleware manages the distributed space, handles data replication,
 * and ensures fault tolerance.
 */
interface SpaceBasedMiddlewareInterface
{
    /**
     * Initialize the middleware.
     *
     * @param SpaceInterface $space The space to manage
     * @return void
     */
    public function initialize(SpaceInterface $space): void;

    /**
     * Register a processing unit with the middleware.
     *
     * @param ProcessingUnitInterface $processingUnit The processing unit to register
     * @return void
     */
    public function registerProcessingUnit(ProcessingUnitInterface $processingUnit): void;

    /**
     * Start all registered processing units.
     *
     * @return void
     */
    public function startProcessingUnits(): void;

    /**
     * Stop all registered processing units.
     *
     * @return void
     */
    public function stopProcessingUnits(): void;

    /**
     * Get all registered processing units.
     *
     * @return array An array of processing units
     */
    public function getProcessingUnits(): array;

    /**
     * Get a processing unit by name.
     *
     * @param string $name The name of the processing unit
     * @return ProcessingUnitInterface|null The processing unit or null if not found
     */
    public function getProcessingUnit(string $name): ?ProcessingUnitInterface;

    /**
     * Replicate data across multiple nodes.
     * In a real-world scenario, this would handle data replication across multiple servers.
     *
     * @param mixed $data The data to replicate
     * @return void
     */
    public function replicateData($data): void;

    /**
     * Partition data across multiple nodes.
     * In a real-world scenario, this would handle data partitioning across multiple servers.
     *
     * @param mixed $data The data to partition
     * @param string $partitionKey The key to use for partitioning
     * @return void
     */
    public function partitionData($data, string $partitionKey): void;

    /**
     * Handle a node failure.
     * In a real-world scenario, this would handle failover and recovery.
     *
     * @param string $nodeId The ID of the failed node
     * @return void
     */
    public function handleNodeFailure(string $nodeId): void;

    /**
     * Get the status of the middleware.
     *
     * @return string The status of the middleware
     */
    public function getStatus(): string;
}
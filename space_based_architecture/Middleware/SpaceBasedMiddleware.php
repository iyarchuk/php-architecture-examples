<?php

namespace SpaceBasedArchitecture\Middleware;

use SpaceBasedArchitecture\Space\SpaceInterface;
use SpaceBasedArchitecture\ProcessingUnits\ProcessingUnitInterface;

/**
 * Implementation of Space-Based Middleware for the space-based architecture example.
 * This is a simplified implementation for demonstration purposes.
 * In a real-world scenario, this would be a distributed, fault-tolerant system.
 */
class SpaceBasedMiddleware implements SpaceBasedMiddlewareInterface
{
    /**
     * The space managed by the middleware.
     *
     * @var SpaceInterface
     */
    protected $space;

    /**
     * The registered processing units.
     *
     * @var array
     */
    protected $processingUnits = [];

    /**
     * The status of the middleware.
     *
     * @var string
     */
    protected $status = 'stopped';

    /**
     * Simulated nodes for data replication and partitioning.
     * In a real-world scenario, these would be actual server nodes.
     *
     * @var array
     */
    protected $nodes = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Simulate a cluster of nodes
        $this->nodes = [
            'node1' => ['status' => 'active', 'data' => []],
            'node2' => ['status' => 'active', 'data' => []],
            'node3' => ['status' => 'active', 'data' => []],
        ];
    }

    /**
     * Initialize the middleware.
     *
     * @param SpaceInterface $space The space to manage
     * @return void
     */
    public function initialize(SpaceInterface $space): void
    {
        $this->space = $space;
        $this->status = 'initialized';
    }

    /**
     * Register a processing unit with the middleware.
     *
     * @param ProcessingUnitInterface $processingUnit The processing unit to register
     * @return void
     */
    public function registerProcessingUnit(ProcessingUnitInterface $processingUnit): void
    {
        $processingUnit->initialize($this->space);
        $this->processingUnits[$processingUnit->getName()] = $processingUnit;
    }

    /**
     * Start all registered processing units.
     *
     * @return void
     */
    public function startProcessingUnits(): void
    {
        foreach ($this->processingUnits as $processingUnit) {
            $processingUnit->start();
        }
        
        $this->status = 'running';
    }

    /**
     * Stop all registered processing units.
     *
     * @return void
     */
    public function stopProcessingUnits(): void
    {
        foreach ($this->processingUnits as $processingUnit) {
            $processingUnit->stop();
        }
        
        $this->status = 'stopped';
    }

    /**
     * Get all registered processing units.
     *
     * @return array An array of processing units
     */
    public function getProcessingUnits(): array
    {
        return $this->processingUnits;
    }

    /**
     * Get a processing unit by name.
     *
     * @param string $name The name of the processing unit
     * @return ProcessingUnitInterface|null The processing unit or null if not found
     */
    public function getProcessingUnit(string $name): ?ProcessingUnitInterface
    {
        return $this->processingUnits[$name] ?? null;
    }

    /**
     * Replicate data across multiple nodes.
     * In a real-world scenario, this would handle data replication across multiple servers.
     *
     * @param mixed $data The data to replicate
     * @return void
     */
    public function replicateData($data): void
    {
        // Simplified data replication for demonstration purposes
        foreach ($this->nodes as $nodeId => &$node) {
            if ($node['status'] === 'active') {
                $node['data'][] = $data;
            }
        }
    }

    /**
     * Partition data across multiple nodes.
     * In a real-world scenario, this would handle data partitioning across multiple servers.
     *
     * @param mixed $data The data to partition
     * @param string $partitionKey The key to use for partitioning
     * @return void
     */
    public function partitionData($data, string $partitionKey): void
    {
        // Simplified data partitioning for demonstration purposes
        // Use a hash of the partition key to determine which node to store the data on
        $hash = crc32($partitionKey);
        $nodeIndex = $hash % count($this->nodes);
        $nodeId = array_keys($this->nodes)[$nodeIndex];
        
        if ($this->nodes[$nodeId]['status'] === 'active') {
            $this->nodes[$nodeId]['data'][] = $data;
        } else {
            // If the node is not active, find the next active node
            $found = false;
            for ($i = 1; $i < count($this->nodes); $i++) {
                $nextNodeIndex = ($nodeIndex + $i) % count($this->nodes);
                $nextNodeId = array_keys($this->nodes)[$nextNodeIndex];
                
                if ($this->nodes[$nextNodeId]['status'] === 'active') {
                    $this->nodes[$nextNodeId]['data'][] = $data;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                // If no active node is found, store the data on all nodes
                $this->replicateData($data);
            }
        }
    }

    /**
     * Handle a node failure.
     * In a real-world scenario, this would handle failover and recovery.
     *
     * @param string $nodeId The ID of the failed node
     * @return void
     */
    public function handleNodeFailure(string $nodeId): void
    {
        if (isset($this->nodes[$nodeId])) {
            $this->nodes[$nodeId]['status'] = 'failed';
            
            // Redistribute the data from the failed node
            $data = $this->nodes[$nodeId]['data'];
            $this->nodes[$nodeId]['data'] = [];
            
            foreach ($data as $item) {
                $this->replicateData($item);
            }
        }
    }

    /**
     * Get the status of the middleware.
     *
     * @return string The status of the middleware
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Get the status of all nodes.
     *
     * @return array The status of all nodes
     */
    public function getNodesStatus(): array
    {
        $status = [];
        
        foreach ($this->nodes as $nodeId => $node) {
            $status[$nodeId] = $node['status'];
        }
        
        return $status;
    }

    /**
     * Activate a node.
     *
     * @param string $nodeId The ID of the node to activate
     * @return bool True if the node was activated, false otherwise
     */
    public function activateNode(string $nodeId): bool
    {
        if (isset($this->nodes[$nodeId])) {
            $this->nodes[$nodeId]['status'] = 'active';
            return true;
        }
        
        return false;
    }

    /**
     * Deactivate a node.
     *
     * @param string $nodeId The ID of the node to deactivate
     * @return bool True if the node was deactivated, false otherwise
     */
    public function deactivateNode(string $nodeId): bool
    {
        if (isset($this->nodes[$nodeId])) {
            $this->nodes[$nodeId]['status'] = 'inactive';
            return true;
        }
        
        return false;
    }
}
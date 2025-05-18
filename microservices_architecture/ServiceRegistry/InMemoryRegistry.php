<?php

namespace MicroservicesArchitecture\ServiceRegistry;

/**
 * InMemoryRegistry
 * 
 * This class provides an in-memory implementation of the Registry interface.
 * It stores service instances in memory and provides methods for service discovery and health monitoring.
 */
class InMemoryRegistry implements Registry
{
    /**
     * @var array The registered services
     */
    private array $services = [];
    
    /**
     * @var array The service instances
     */
    private array $instances = [];
    
    /**
     * @var array Round-robin counters for load balancing
     */
    private array $roundRobinCounters = [];
    
    /**
     * Register a service instance
     * 
     * @param string $serviceName The name of the service
     * @param string $serviceUrl The URL of the service instance
     * @param array $metadata Additional metadata about the service
     * @return string The ID of the registered service instance
     */
    public function registerService(string $serviceName, string $serviceUrl, array $metadata = []): string
    {
        $serviceId = $this->generateServiceId();
        
        // Add to services list if not already there
        if (!isset($this->services[$serviceName])) {
            $this->services[$serviceName] = [];
            $this->roundRobinCounters[$serviceName] = 0;
        }
        
        // Register the instance
        $this->services[$serviceName][] = $serviceId;
        $this->instances[$serviceId] = [
            'id' => $serviceId,
            'name' => $serviceName,
            'url' => $serviceUrl,
            'metadata' => $metadata,
            'healthy' => true,
            'registeredAt' => date('Y-m-d H:i:s'),
            'lastUpdated' => date('Y-m-d H:i:s')
        ];
        
        return $serviceId;
    }
    
    /**
     * Deregister a service instance
     * 
     * @param string $serviceId The ID of the service instance to deregister
     * @return bool True if the service was deregistered, false otherwise
     */
    public function deregisterService(string $serviceId): bool
    {
        if (!isset($this->instances[$serviceId])) {
            return false;
        }
        
        $serviceName = $this->instances[$serviceId]['name'];
        
        // Remove from services list
        if (isset($this->services[$serviceName])) {
            $this->services[$serviceName] = array_filter(
                $this->services[$serviceName],
                function ($id) use ($serviceId) {
                    return $id !== $serviceId;
                }
            );
            
            // If no more instances, remove the service
            if (empty($this->services[$serviceName])) {
                unset($this->services[$serviceName]);
                unset($this->roundRobinCounters[$serviceName]);
            }
        }
        
        // Remove the instance
        unset($this->instances[$serviceId]);
        
        return true;
    }
    
    /**
     * Get all instances of a service
     * 
     * @param string $serviceName The name of the service
     * @return array An array of service instances
     */
    public function getServiceInstances(string $serviceName): array
    {
        if (!isset($this->services[$serviceName])) {
            return [];
        }
        
        $instances = [];
        foreach ($this->services[$serviceName] as $serviceId) {
            if (isset($this->instances[$serviceId]) && $this->instances[$serviceId]['healthy']) {
                $instances[] = $this->instances[$serviceId];
            }
        }
        
        return $instances;
    }
    
    /**
     * Get a specific service instance
     * 
     * @param string $serviceId The ID of the service instance
     * @return array|null The service instance data, or null if not found
     */
    public function getServiceInstance(string $serviceId): ?array
    {
        return $this->instances[$serviceId] ?? null;
    }
    
    /**
     * Get a service instance using a load balancing strategy
     * 
     * @param string $serviceName The name of the service
     * @param string $strategy The load balancing strategy to use (e.g., "random", "round-robin")
     * @return array|null The selected service instance, or null if no instances are available
     */
    public function getServiceInstanceWithLoadBalancing(string $serviceName, string $strategy = 'random'): ?array
    {
        $instances = $this->getServiceInstances($serviceName);
        
        if (empty($instances)) {
            return null;
        }
        
        switch ($strategy) {
            case 'round-robin':
                return $this->roundRobinStrategy($serviceName, $instances);
            case 'random':
            default:
                return $this->randomStrategy($instances);
        }
    }
    
    /**
     * Update the health status of a service instance
     * 
     * @param string $serviceId The ID of the service instance
     * @param bool $isHealthy Whether the service is healthy
     * @return bool True if the status was updated, false otherwise
     */
    public function updateHealthStatus(string $serviceId, bool $isHealthy): bool
    {
        if (!isset($this->instances[$serviceId])) {
            return false;
        }
        
        $this->instances[$serviceId]['healthy'] = $isHealthy;
        $this->instances[$serviceId]['lastUpdated'] = date('Y-m-d H:i:s');
        
        return true;
    }
    
    /**
     * Get all registered services
     * 
     * @return array An array of all registered services
     */
    public function getAllServices(): array
    {
        $result = [];
        foreach ($this->services as $serviceName => $serviceIds) {
            $result[$serviceName] = $this->getServiceInstances($serviceName);
        }
        
        return $result;
    }
    
    /**
     * Generate a unique service ID
     * 
     * @return string A unique service ID
     */
    private function generateServiceId(): string
    {
        return uniqid('svc_', true);
    }
    
    /**
     * Round-robin load balancing strategy
     * 
     * @param string $serviceName The name of the service
     * @param array $instances The available service instances
     * @return array The selected service instance
     */
    private function roundRobinStrategy(string $serviceName, array $instances): array
    {
        $count = count($instances);
        $index = $this->roundRobinCounters[$serviceName] % $count;
        $this->roundRobinCounters[$serviceName]++;
        
        return $instances[$index];
    }
    
    /**
     * Random load balancing strategy
     * 
     * @param array $instances The available service instances
     * @return array The selected service instance
     */
    private function randomStrategy(array $instances): array
    {
        $count = count($instances);
        $index = rand(0, $count - 1);
        
        return $instances[$index];
    }
}
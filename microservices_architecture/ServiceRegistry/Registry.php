<?php

namespace MicroservicesArchitecture\ServiceRegistry;

/**
 * Registry
 * 
 * This interface defines the contract for a service registry.
 * A service registry is responsible for keeping track of available service instances,
 * enabling service discovery, and providing health monitoring.
 */
interface Registry
{
    /**
     * Register a service instance
     * 
     * @param string $serviceName The name of the service
     * @param string $serviceUrl The URL of the service instance
     * @param array $metadata Additional metadata about the service
     * @return string The ID of the registered service instance
     */
    public function registerService(string $serviceName, string $serviceUrl, array $metadata = []): string;
    
    /**
     * Deregister a service instance
     * 
     * @param string $serviceId The ID of the service instance to deregister
     * @return bool True if the service was deregistered, false otherwise
     */
    public function deregisterService(string $serviceId): bool;
    
    /**
     * Get all instances of a service
     * 
     * @param string $serviceName The name of the service
     * @return array An array of service instances
     */
    public function getServiceInstances(string $serviceName): array;
    
    /**
     * Get a specific service instance
     * 
     * @param string $serviceId The ID of the service instance
     * @return array|null The service instance data, or null if not found
     */
    public function getServiceInstance(string $serviceId): ?array;
    
    /**
     * Get a service instance using a load balancing strategy
     * 
     * @param string $serviceName The name of the service
     * @param string $strategy The load balancing strategy to use (e.g., "random", "round-robin")
     * @return array|null The selected service instance, or null if no instances are available
     */
    public function getServiceInstanceWithLoadBalancing(string $serviceName, string $strategy = 'random'): ?array;
    
    /**
     * Update the health status of a service instance
     * 
     * @param string $serviceId The ID of the service instance
     * @param bool $isHealthy Whether the service is healthy
     * @return bool True if the status was updated, false otherwise
     */
    public function updateHealthStatus(string $serviceId, bool $isHealthy): bool;
    
    /**
     * Get all registered services
     * 
     * @return array An array of all registered services
     */
    public function getAllServices(): array;
}
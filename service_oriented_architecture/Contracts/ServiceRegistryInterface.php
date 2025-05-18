<?php

namespace ServiceOrientedArchitecture\Contracts;

/**
 * ServiceRegistryInterface defines the contract for the Service Registry.
 * In a Service-Oriented Architecture, a service registry is used to register
 * and discover services.
 */
interface ServiceRegistryInterface
{
    /**
     * Register a service with the registry
     *
     * @param string $serviceName The name of the service
     * @param string $serviceUrl The URL of the service
     * @param array $metadata Additional metadata about the service
     * @return bool True if the service was registered successfully
     */
    public function registerService(string $serviceName, string $serviceUrl, array $metadata = []): bool;
    
    /**
     * Unregister a service from the registry
     *
     * @param string $serviceName The name of the service
     * @param string $serviceUrl The URL of the service
     * @return bool True if the service was unregistered successfully
     */
    public function unregisterService(string $serviceName, string $serviceUrl): bool;
    
    /**
     * Discover a service by name
     *
     * @param string $serviceName The name of the service to discover
     * @return array|null The service information if found, null otherwise
     */
    public function discoverService(string $serviceName): ?array;
    
    /**
     * List all registered services
     *
     * @param array $filters Optional filters
     * @return array The list of registered services
     */
    public function listServices(array $filters = []): array;
    
    /**
     * Check if a service is alive
     *
     * @param string $serviceName The name of the service
     * @param string $serviceUrl The URL of the service
     * @return bool True if the service is alive
     */
    public function isServiceAlive(string $serviceName, string $serviceUrl): bool;
    
    /**
     * Update service metadata
     *
     * @param string $serviceName The name of the service
     * @param string $serviceUrl The URL of the service
     * @param array $metadata The new metadata
     * @return bool True if the metadata was updated successfully
     */
    public function updateServiceMetadata(string $serviceName, string $serviceUrl, array $metadata): bool;
}
<?php

namespace ServiceOrientedArchitecture\Registry;

use ServiceOrientedArchitecture\Contracts\ServiceRegistryInterface;

/**
 * ServiceRegistry implements the ServiceRegistryInterface.
 * In a Service-Oriented Architecture, a service registry is used to register
 * and discover services.
 */
class ServiceRegistry implements ServiceRegistryInterface
{
    /**
     * @var array In-memory service registry for this example
     */
    private $services = [];
    
    /**
     * Register a service with the registry
     *
     * @param string $serviceName The name of the service
     * @param string $serviceUrl The URL of the service
     * @param array $metadata Additional metadata about the service
     * @return bool True if the service was registered successfully
     */
    public function registerService(string $serviceName, string $serviceUrl, array $metadata = []): bool
    {
        // Check if service already exists
        foreach ($this->services as $service) {
            if ($service['name'] === $serviceName && $service['url'] === $serviceUrl) {
                return false; // Service already registered
            }
        }
        
        // Register the service
        $this->services[] = [
            'name' => $serviceName,
            'url' => $serviceUrl,
            'metadata' => $metadata,
            'registered_at' => date('Y-m-d H:i:s'),
            'last_heartbeat' => date('Y-m-d H:i:s'),
            'status' => 'active'
        ];
        
        return true;
    }
    
    /**
     * Unregister a service from the registry
     *
     * @param string $serviceName The name of the service
     * @param string $serviceUrl The URL of the service
     * @return bool True if the service was unregistered successfully
     */
    public function unregisterService(string $serviceName, string $serviceUrl): bool
    {
        foreach ($this->services as $key => $service) {
            if ($service['name'] === $serviceName && $service['url'] === $serviceUrl) {
                unset($this->services[$key]);
                $this->services = array_values($this->services); // Re-index the array
                return true;
            }
        }
        
        return false; // Service not found
    }
    
    /**
     * Discover a service by name
     *
     * @param string $serviceName The name of the service to discover
     * @return array|null The service information if found, null otherwise
     */
    public function discoverService(string $serviceName): ?array
    {
        // Find all services with the given name
        $matchingServices = array_filter($this->services, function ($service) use ($serviceName) {
            return $service['name'] === $serviceName && $service['status'] === 'active';
        });
        
        if (empty($matchingServices)) {
            return null; // No matching services found
        }
        
        // Return the first active service (in a real implementation, we might use load balancing)
        return reset($matchingServices);
    }
    
    /**
     * List all registered services
     *
     * @param array $filters Optional filters
     * @return array The list of registered services
     */
    public function listServices(array $filters = []): array
    {
        $services = $this->services;
        
        // Apply filters if any
        if (!empty($filters)) {
            $services = array_filter($services, function ($service) use ($filters) {
                foreach ($filters as $key => $value) {
                    if ($key === 'metadata' && is_array($value)) {
                        // Filter by metadata
                        foreach ($value as $metaKey => $metaValue) {
                            if (!isset($service['metadata'][$metaKey]) || $service['metadata'][$metaKey] !== $metaValue) {
                                return false;
                            }
                        }
                    } elseif (!isset($service[$key]) || $service[$key] !== $value) {
                        return false;
                    }
                }
                return true;
            });
        }
        
        return array_values($services);
    }
    
    /**
     * Check if a service is alive
     *
     * @param string $serviceName The name of the service
     * @param string $serviceUrl The URL of the service
     * @return bool True if the service is alive
     */
    public function isServiceAlive(string $serviceName, string $serviceUrl): bool
    {
        // In a real implementation, we would make a health check request to the service
        // For this example, we'll just check if the service is registered and active
        foreach ($this->services as $key => $service) {
            if ($service['name'] === $serviceName && $service['url'] === $serviceUrl) {
                if ($service['status'] === 'active') {
                    // Update the last heartbeat
                    $this->services[$key]['last_heartbeat'] = date('Y-m-d H:i:s');
                    return true;
                }
                return false;
            }
        }
        
        return false; // Service not found
    }
    
    /**
     * Update service metadata
     *
     * @param string $serviceName The name of the service
     * @param string $serviceUrl The URL of the service
     * @param array $metadata The new metadata
     * @return bool True if the metadata was updated successfully
     */
    public function updateServiceMetadata(string $serviceName, string $serviceUrl, array $metadata): bool
    {
        foreach ($this->services as $key => $service) {
            if ($service['name'] === $serviceName && $service['url'] === $serviceUrl) {
                // Update the metadata
                $this->services[$key]['metadata'] = array_merge($service['metadata'], $metadata);
                return true;
            }
        }
        
        return false; // Service not found
    }
    
    /**
     * Get all services
     *
     * @return array All registered services
     */
    public function getAllServices(): array
    {
        return $this->services;
    }
    
    /**
     * Mark a service as inactive
     *
     * @param string $serviceName The name of the service
     * @param string $serviceUrl The URL of the service
     * @return bool True if the service was marked as inactive
     */
    public function markServiceAsInactive(string $serviceName, string $serviceUrl): bool
    {
        foreach ($this->services as $key => $service) {
            if ($service['name'] === $serviceName && $service['url'] === $serviceUrl) {
                $this->services[$key]['status'] = 'inactive';
                return true;
            }
        }
        
        return false; // Service not found
    }
    
    /**
     * Mark a service as active
     *
     * @param string $serviceName The name of the service
     * @param string $serviceUrl The URL of the service
     * @return bool True if the service was marked as active
     */
    public function markServiceAsActive(string $serviceName, string $serviceUrl): bool
    {
        foreach ($this->services as $key => $service) {
            if ($service['name'] === $serviceName && $service['url'] === $serviceUrl) {
                $this->services[$key]['status'] = 'active';
                $this->services[$key]['last_heartbeat'] = date('Y-m-d H:i:s');
                return true;
            }
        }
        
        return false; // Service not found
    }
}
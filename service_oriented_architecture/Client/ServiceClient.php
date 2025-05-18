<?php

namespace ServiceOrientedArchitecture\Client;

use ServiceOrientedArchitecture\Contracts\ServiceRegistryInterface;

/**
 * ServiceClient provides a client for interacting with services.
 * In a Service-Oriented Architecture, clients use service discovery to find
 * and communicate with services.
 */
class ServiceClient
{
    /**
     * @var ServiceRegistryInterface The service registry
     */
    private $registry;
    
    /**
     * @var array Cache of discovered services
     */
    private $serviceCache = [];
    
    /**
     * Constructor
     *
     * @param ServiceRegistryInterface $registry The service registry
     */
    public function __construct(ServiceRegistryInterface $registry)
    {
        $this->registry = $registry;
    }
    
    /**
     * Call a service method
     *
     * @param string $serviceName The name of the service
     * @param string $method The method to call
     * @param array $params The parameters to pass to the method
     * @return array The result of the service call
     * @throws \Exception If the service is not found or the method call fails
     */
    public function call(string $serviceName, string $method, array $params = []): array
    {
        // Discover the service
        $service = $this->discoverService($serviceName);
        
        if (!$service) {
            throw new \Exception("Service '$serviceName' not found");
        }
        
        // In a real implementation, we would make an HTTP request to the service
        // For this example, we'll simulate the call
        return $this->simulateServiceCall($service, $method, $params);
    }
    
    /**
     * Discover a service by name
     *
     * @param string $serviceName The name of the service
     * @param bool $useCache Whether to use the cache
     * @return array|null The service information if found, null otherwise
     */
    public function discoverService(string $serviceName, bool $useCache = true): ?array
    {
        // Check the cache first if enabled
        if ($useCache && isset($this->serviceCache[$serviceName])) {
            return $this->serviceCache[$serviceName];
        }
        
        // Discover the service
        $service = $this->registry->discoverService($serviceName);
        
        // Cache the service if found
        if ($service) {
            $this->serviceCache[$serviceName] = $service;
        }
        
        return $service;
    }
    
    /**
     * Clear the service cache
     *
     * @param string|null $serviceName The name of the service to clear from the cache, or null to clear all
     */
    public function clearCache(?string $serviceName = null): void
    {
        if ($serviceName === null) {
            $this->serviceCache = [];
        } elseif (isset($this->serviceCache[$serviceName])) {
            unset($this->serviceCache[$serviceName]);
        }
    }
    
    /**
     * Simulate a service call
     *
     * @param array $service The service information
     * @param string $method The method to call
     * @param array $params The parameters to pass to the method
     * @return array The result of the service call
     * @throws \Exception If the method call fails
     */
    private function simulateServiceCall(array $service, string $method, array $params): array
    {
        // In a real implementation, we would make an HTTP request to the service
        // For this example, we'll simulate the call based on the service name
        
        // Check if the service is alive
        if (!$this->registry->isServiceAlive($service['name'], $service['url'])) {
            throw new \Exception("Service '{$service['name']}' is not available");
        }
        
        // Simulate different service calls
        switch ($service['name']) {
            case 'user-service':
                return $this->simulateUserServiceCall($method, $params);
            case 'product-service':
                return $this->simulateProductServiceCall($method, $params);
            default:
                throw new \Exception("Unsupported service: '{$service['name']}'");
        }
    }
    
    /**
     * Simulate a user service call
     *
     * @param string $method The method to call
     * @param array $params The parameters to pass to the method
     * @return array The result of the service call
     * @throws \Exception If the method call fails
     */
    private function simulateUserServiceCall(string $method, array $params): array
    {
        // Create a user service instance
        $userService = new \ServiceOrientedArchitecture\Services\UserService();
        
        // Call the appropriate method
        switch ($method) {
            case 'createUser':
                return $userService->createUser($params);
            case 'getUser':
                return $userService->getUser($params['userId'] ?? '');
            case 'updateUser':
                return $userService->updateUser($params['userId'] ?? '', $params['userData'] ?? []);
            case 'deleteUser':
                return $userService->deleteUser($params['userId'] ?? '');
            case 'listUsers':
                return $userService->listUsers($params['filters'] ?? []);
            default:
                throw new \Exception("Unsupported method: '$method'");
        }
    }
    
    /**
     * Simulate a product service call
     *
     * @param string $method The method to call
     * @param array $params The parameters to pass to the method
     * @return array The result of the service call
     * @throws \Exception If the method call fails
     */
    private function simulateProductServiceCall(string $method, array $params): array
    {
        // Create a product service instance
        $productService = new \ServiceOrientedArchitecture\Services\ProductService();
        
        // Call the appropriate method
        switch ($method) {
            case 'createProduct':
                return $productService->createProduct($params);
            case 'getProduct':
                return $productService->getProduct($params['productId'] ?? '');
            case 'updateProduct':
                return $productService->updateProduct($params['productId'] ?? '', $params['productData'] ?? []);
            case 'deleteProduct':
                return $productService->deleteProduct($params['productId'] ?? '');
            case 'listProducts':
                return $productService->listProducts($params['filters'] ?? []);
            case 'searchProducts':
                return $productService->searchProducts($params['query'] ?? '', $params['options'] ?? []);
            default:
                throw new \Exception("Unsupported method: '$method'");
        }
    }
}
<?php

namespace MicroservicesArchitecture\ApiGateway;

/**
 * Gateway
 * 
 * This interface defines the contract for an API Gateway.
 * An API Gateway is responsible for routing requests to appropriate services,
 * aggregating responses from multiple services, and handling cross-cutting concerns.
 */
interface Gateway
{
    /**
     * Route a request to a service
     * 
     * @param string $serviceName The name of the service to route to
     * @param string $endpoint The endpoint to call on the service
     * @param string $method The HTTP method to use (GET, POST, etc.)
     * @param array $data The data to send with the request
     * @param array $headers The headers to send with the request
     * @return array The response from the service
     */
    public function routeRequest(
        string $serviceName,
        string $endpoint,
        string $method = 'GET',
        array $data = [],
        array $headers = []
    ): array;
    
    /**
     * Aggregate responses from multiple services
     * 
     * @param array $requests An array of request specifications
     * @return array The aggregated responses
     */
    public function aggregateResponses(array $requests): array;
    
    /**
     * Apply a middleware to the gateway
     * 
     * @param callable $middleware The middleware function
     * @return self
     */
    public function addMiddleware(callable $middleware): self;
    
    /**
     * Set the authentication handler
     * 
     * @param callable $handler The authentication handler function
     * @return self
     */
    public function setAuthenticationHandler(callable $handler): self;
    
    /**
     * Set the rate limiting handler
     * 
     * @param callable $handler The rate limiting handler function
     * @return self
     */
    public function setRateLimitingHandler(callable $handler): self;
    
    /**
     * Set the circuit breaker
     * 
     * @param object $circuitBreaker The circuit breaker instance
     * @return self
     */
    public function setCircuitBreaker(object $circuitBreaker): self;
    
    /**
     * Set the service registry
     * 
     * @param object $registry The service registry instance
     * @return self
     */
    public function setServiceRegistry(object $registry): self;
}
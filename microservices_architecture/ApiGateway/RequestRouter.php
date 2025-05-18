<?php

namespace MicroservicesArchitecture\ApiGateway;

use MicroservicesArchitecture\ServiceRegistry\Registry;
use MicroservicesArchitecture\CircuitBreaker\CircuitBreaker;

/**
 * RequestRouter
 * 
 * This class implements the Gateway interface and provides routing functionality for the API Gateway.
 * It routes requests to appropriate services, aggregates responses, and handles cross-cutting concerns.
 */
class RequestRouter implements Gateway
{
    /**
     * @var Registry The service registry
     */
    private Registry $registry;
    
    /**
     * @var CircuitBreaker|null The circuit breaker
     */
    private ?CircuitBreaker $circuitBreaker = null;
    
    /**
     * @var array The middleware stack
     */
    private array $middleware = [];
    
    /**
     * @var callable|null The authentication handler
     */
    private $authHandler = null;
    
    /**
     * @var callable|null The rate limiting handler
     */
    private $rateLimitHandler = null;
    
    /**
     * Constructor
     * 
     * @param Registry $registry The service registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }
    
    /**
     * Route a request to a service
     * 
     * @param string $serviceName The name of the service to route to
     * @param string $endpoint The endpoint to call on the service
     * @param string $method The HTTP method to use (GET, POST, etc.)
     * @param array $data The data to send with the request
     * @param array $headers The headers to send with the request
     * @return array The response from the service
     * @throws \Exception If the service is not available
     */
    public function routeRequest(
        string $serviceName,
        string $endpoint,
        string $method = 'GET',
        array $data = [],
        array $headers = []
    ): array {
        // Apply middleware
        $request = [
            'service' => $serviceName,
            'endpoint' => $endpoint,
            'method' => $method,
            'data' => $data,
            'headers' => $headers
        ];
        
        foreach ($this->middleware as $middleware) {
            $request = $middleware($request);
        }
        
        // Apply authentication if set
        if ($this->authHandler) {
            $authResult = ($this->authHandler)($request);
            if (!$authResult['authenticated']) {
                return [
                    'status' => 'error',
                    'code' => 401,
                    'message' => $authResult['message'] ?? 'Unauthorized'
                ];
            }
        }
        
        // Apply rate limiting if set
        if ($this->rateLimitHandler) {
            $rateLimitResult = ($this->rateLimitHandler)($request);
            if (!$rateLimitResult['allowed']) {
                return [
                    'status' => 'error',
                    'code' => 429,
                    'message' => $rateLimitResult['message'] ?? 'Too Many Requests'
                ];
            }
        }
        
        // Get service instance with load balancing
        $serviceInstance = $this->registry->getServiceInstanceWithLoadBalancing($serviceName);
        
        if (!$serviceInstance) {
            return [
                'status' => 'error',
                'code' => 503,
                'message' => "Service '$serviceName' is not available"
            ];
        }
        
        // Check circuit breaker if set
        if ($this->circuitBreaker && !$this->circuitBreaker->isAllowed($serviceName)) {
            return [
                'status' => 'error',
                'code' => 503,
                'message' => "Service '$serviceName' is temporarily unavailable (circuit open)"
            ];
        }
        
        try {
            // In a real implementation, this would make an HTTP request to the service
            // For this example, we'll simulate a response
            $response = $this->simulateServiceCall(
                $serviceInstance['url'],
                $endpoint,
                $method,
                $data,
                $headers
            );
            
            // Record success in circuit breaker if set
            if ($this->circuitBreaker) {
                $this->circuitBreaker->recordSuccess($serviceName);
            }
            
            return $response;
        } catch (\Exception $e) {
            // Record failure in circuit breaker if set
            if ($this->circuitBreaker) {
                $this->circuitBreaker->recordFailure($serviceName);
            }
            
            throw $e;
        }
    }
    
    /**
     * Aggregate responses from multiple services
     * 
     * @param array $requests An array of request specifications
     * @return array The aggregated responses
     */
    public function aggregateResponses(array $requests): array
    {
        $responses = [];
        
        foreach ($requests as $key => $request) {
            try {
                $responses[$key] = $this->routeRequest(
                    $request['service'],
                    $request['endpoint'],
                    $request['method'] ?? 'GET',
                    $request['data'] ?? [],
                    $request['headers'] ?? []
                );
            } catch (\Exception $e) {
                $responses[$key] = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => $e->getMessage()
                ];
            }
        }
        
        return $responses;
    }
    
    /**
     * Apply a middleware to the gateway
     * 
     * @param callable $middleware The middleware function
     * @return self
     */
    public function addMiddleware(callable $middleware): self
    {
        $this->middleware[] = $middleware;
        return $this;
    }
    
    /**
     * Set the authentication handler
     * 
     * @param callable $handler The authentication handler function
     * @return self
     */
    public function setAuthenticationHandler(callable $handler): self
    {
        $this->authHandler = $handler;
        return $this;
    }
    
    /**
     * Set the rate limiting handler
     * 
     * @param callable $handler The rate limiting handler function
     * @return self
     */
    public function setRateLimitingHandler(callable $handler): self
    {
        $this->rateLimitHandler = $handler;
        return $this;
    }
    
    /**
     * Set the circuit breaker
     * 
     * @param object $circuitBreaker The circuit breaker instance
     * @return self
     */
    public function setCircuitBreaker(object $circuitBreaker): self
    {
        $this->circuitBreaker = $circuitBreaker;
        return $this;
    }
    
    /**
     * Set the service registry
     * 
     * @param object $registry The service registry instance
     * @return self
     */
    public function setServiceRegistry(object $registry): self
    {
        $this->registry = $registry;
        return $this;
    }
    
    /**
     * Simulate a service call
     * 
     * @param string $serviceUrl The URL of the service
     * @param string $endpoint The endpoint to call
     * @param string $method The HTTP method to use
     * @param array $data The data to send
     * @param array $headers The headers to send
     * @return array The simulated response
     */
    private function simulateServiceCall(
        string $serviceUrl,
        string $endpoint,
        string $method,
        array $data,
        array $headers
    ): array {
        // In a real implementation, this would make an HTTP request to the service
        // For this example, we'll return a simulated response
        return [
            'status' => 'success',
            'code' => 200,
            'data' => [
                'message' => "Response from $serviceUrl/$endpoint",
                'method' => $method,
                'receivedData' => $data,
                'receivedHeaders' => $headers,
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ];
    }
}
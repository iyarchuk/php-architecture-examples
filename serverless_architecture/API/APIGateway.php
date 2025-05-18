<?php

namespace ServerlessArchitecture\API;

/**
 * APIGateway handles API requests in a serverless environment.
 * In a serverless architecture, API Gateway acts as the entry point for HTTP requests
 * and routes them to the appropriate functions.
 */
class APIGateway
{
    /**
     * @var array The routes configuration
     */
    private $routes = [];
    
    /**
     * @var array The API configuration
     */
    private $config;
    
    /**
     * Constructor
     *
     * @param array $config The API configuration
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'name' => 'ServerlessAPI',
            'version' => 'v1',
            'basePath' => '/api',
            'cors' => true
        ], $config);
    }
    
    /**
     * Add a route to the API Gateway
     *
     * @param string $method The HTTP method (GET, POST, PUT, DELETE)
     * @param string $path The path for the route
     * @param string $functionName The name of the function to invoke
     * @param array $options Additional options for the route
     * @return APIGateway Returns $this for method chaining
     */
    public function addRoute(string $method, string $path, string $functionName, array $options = []): APIGateway
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'functionName' => $functionName,
            'options' => array_merge([
                'auth' => false,
                'cors' => $this->config['cors'],
                'rateLimit' => 100
            ], $options)
        ];
        
        return $this;
    }
    
    /**
     * Process an API request
     *
     * @param string $method The HTTP method
     * @param string $path The request path
     * @param array $headers The request headers
     * @param array $queryParams The query parameters
     * @param array $body The request body
     * @return array The response
     */
    public function processRequest(string $method, string $path, array $headers = [], array $queryParams = [], array $body = []): array
    {
        // Find the matching route
        $route = $this->findRoute($method, $path);
        
        if (!$route) {
            return [
                'statusCode' => 404,
                'body' => json_encode([
                    'message' => 'Route not found'
                ])
            ];
        }
        
        // Check authentication if required
        if ($route['options']['auth'] && !$this->isAuthenticated($headers)) {
            return [
                'statusCode' => 401,
                'body' => json_encode([
                    'message' => 'Unauthorized'
                ])
            ];
        }
        
        // In a real application, we would invoke the function
        // For this example, we'll just return a success response
        return [
            'statusCode' => 200,
            'body' => json_encode([
                'message' => 'Request processed successfully',
                'data' => [
                    'method' => $method,
                    'path' => $path,
                    'functionName' => $route['functionName'],
                    'queryParams' => $queryParams,
                    'body' => $body
                ]
            ])
        ];
    }
    
    /**
     * Find a route that matches the method and path
     *
     * @param string $method The HTTP method
     * @param string $path The request path
     * @return array|null The matching route or null if not found
     */
    private function findRoute(string $method, string $path): ?array
    {
        $method = strtoupper($method);
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->pathMatches($route['path'], $path)) {
                return $route;
            }
        }
        
        return null;
    }
    
    /**
     * Check if a path matches a route pattern
     *
     * @param string $pattern The route pattern
     * @param string $path The request path
     * @return bool True if the path matches the pattern
     */
    private function pathMatches(string $pattern, string $path): bool
    {
        // Simple implementation for this example
        // In a real application, we would use a more sophisticated matching algorithm
        $patternParts = explode('/', trim($pattern, '/'));
        $pathParts = explode('/', trim($path, '/'));
        
        if (count($patternParts) !== count($pathParts)) {
            return false;
        }
        
        for ($i = 0; $i < count($patternParts); $i++) {
            if (strpos($patternParts[$i], '{') === 0 && strpos($patternParts[$i], '}') === strlen($patternParts[$i]) - 1) {
                // This is a path parameter, so it matches any value
                continue;
            }
            
            if ($patternParts[$i] !== $pathParts[$i]) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check if a request is authenticated
     *
     * @param array $headers The request headers
     * @return bool True if the request is authenticated
     */
    private function isAuthenticated(array $headers): bool
    {
        // Simple implementation for this example
        // In a real application, we would validate the token with a proper authentication service
        return isset($headers['Authorization']) && strpos($headers['Authorization'], 'Bearer ') === 0;
    }
    
    /**
     * Get the routes configuration
     *
     * @return array The routes
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
    
    /**
     * Get the API configuration
     *
     * @return array The configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
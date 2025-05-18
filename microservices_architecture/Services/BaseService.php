<?php

namespace MicroservicesArchitecture\Services;

use MicroservicesArchitecture\ServiceRegistry\Registry;
use MicroservicesArchitecture\MessageBroker\Broker;

/**
 * BaseService
 * 
 * This abstract class provides common functionality for all microservices.
 * It implements the Service interface and provides default implementations
 * for common methods.
 */
abstract class BaseService implements Service
{
    /**
     * @var string The name of the service
     */
    protected string $name;

    /**
     * @var string The URL of the service
     */
    protected string $url;

    /**
     * @var array The endpoints supported by the service
     */
    protected array $endpoints = [];

    /**
     * @var string|null The ID of the registered service
     */
    protected ?string $serviceId = null;

    /**
     * @var array The topics to subscribe to
     */
    protected array $subscribeTopics = [];

    /**
     * Constructor
     * 
     * @param string $name The name of the service
     * @param string $url The URL of the service
     */
    public function __construct(string $name, string $url)
    {
        $this->name = $name;
        $this->url = $url;
        $this->initializeEndpoints();
    }

    /**
     * Initialize the endpoints supported by the service
     * 
     * @return void
     */
    abstract protected function initializeEndpoints(): void;

    /**
     * Get the name of the service
     * 
     * @return string The name of the service
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the URL of the service
     * 
     * @return string The URL of the service
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Handle a request to the service
     * 
     * @param string $endpoint The endpoint to call
     * @param string $method The HTTP method to use
     * @param array $data The data to send with the request
     * @param array $headers The headers to send with the request
     * @return array The response from the service
     * @throws \InvalidArgumentException If the endpoint is not supported
     */
    public function handleRequest(
        string $endpoint,
        string $method = 'GET',
        array $data = [],
        array $headers = []
    ): array {
        // Find the matching endpoint pattern
        $matchedEndpoint = null;
        $extractedParams = [];

        foreach ($this->endpoints as $pattern => $config) {
            // Check for exact match first
            if ($pattern === $endpoint) {
                $matchedEndpoint = $pattern;
                break;
            }

            // Check for parameterized endpoint
            if (strpos($pattern, '{') !== false) {
                // Convert the pattern to a regex
                $regex = preg_replace('/\{([^}]+)\}/', '(?P<$1>[^/]+)', $pattern);
                $regex = '#^' . $regex . '$#';

                if (preg_match($regex, $endpoint, $matches)) {
                    $matchedEndpoint = $pattern;

                    // Extract parameters
                    foreach ($matches as $key => $value) {
                        if (!is_numeric($key)) {
                            $extractedParams[$key] = $value;
                        }
                    }

                    break;
                }
            }
        }

        // Check if the endpoint is supported
        if ($matchedEndpoint === null) {
            throw new \InvalidArgumentException("Endpoint '$endpoint' is not supported by the {$this->name} service");
        }

        // Check if the method is supported for the endpoint
        $supportedMethods = $this->endpoints[$matchedEndpoint]['methods'] ?? ['GET'];
        if (!in_array($method, $supportedMethods)) {
            throw new \InvalidArgumentException("Method '$method' is not supported for endpoint '$endpoint'");
        }

        // Get the handler for the endpoint
        $handler = $this->endpoints[$matchedEndpoint]['handler'] ?? null;

        if (!is_callable($handler)) {
            throw new \InvalidArgumentException("No handler defined for endpoint '$endpoint'");
        }

        // Add extracted parameters to the data
        $data = array_merge($data, $extractedParams);

        // Call the handler
        return $handler($method, $data, $headers);
    }

    /**
     * Register the service with the service registry
     * 
     * @param object $registry The service registry
     * @return string The ID of the registered service
     */
    public function register(object $registry): string
    {
        if (!$registry instanceof Registry) {
            throw new \InvalidArgumentException('Registry must implement the Registry interface');
        }

        $metadata = [
            'endpoints' => array_keys($this->endpoints),
            'version' => '1.0.0',
            'healthCheckEndpoint' => '/health'
        ];

        $this->serviceId = $registry->registerService($this->name, $this->url, $metadata);

        return $this->serviceId;
    }

    /**
     * Deregister the service from the service registry
     * 
     * @param object $registry The service registry
     * @param string $serviceId The ID of the registered service
     * @return bool True if the service was deregistered successfully
     */
    public function deregister(object $registry, string $serviceId): bool
    {
        if (!$registry instanceof Registry) {
            throw new \InvalidArgumentException('Registry must implement the Registry interface');
        }

        $result = $registry->deregisterService($serviceId);

        if ($result && $this->serviceId === $serviceId) {
            $this->serviceId = null;
        }

        return $result;
    }

    /**
     * Subscribe to events from the message broker
     * 
     * @param object $broker The message broker
     * @return bool True if the subscription was successful
     */
    public function subscribeToEvents(object $broker): bool
    {
        if (!$broker instanceof Broker) {
            throw new \InvalidArgumentException('Broker must implement the Broker interface');
        }

        $success = true;

        foreach ($this->subscribeTopics as $topic => $handler) {
            $subscriberId = $this->name . '_' . $topic;
            $result = $broker->subscribe($topic, $handler, $subscriberId);
            $success = $success && $result;
        }

        return $success;
    }

    /**
     * Publish an event to the message broker
     * 
     * @param object $broker The message broker
     * @param string $topic The topic to publish to
     * @param array $event The event to publish
     * @return bool True if the event was published successfully
     */
    public function publishEvent(object $broker, string $topic, array $event): bool
    {
        if (!$broker instanceof Broker) {
            throw new \InvalidArgumentException('Broker must implement the Broker interface');
        }

        $headers = [
            'source' => $this->name,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        return $broker->publish($topic, $event, $headers);
    }
}

<?php

namespace MicroservicesArchitecture\Services;

/**
 * Service
 * 
 * This interface defines the contract for a microservice.
 * Each microservice implements a specific business capability and
 * communicates with other services via well-defined APIs.
 */
interface Service
{
    /**
     * Get the name of the service
     * 
     * @return string The name of the service
     */
    public function getName(): string;
    
    /**
     * Get the URL of the service
     * 
     * @return string The URL of the service
     */
    public function getUrl(): string;
    
    /**
     * Handle a request to the service
     * 
     * @param string $endpoint The endpoint to call
     * @param string $method The HTTP method to use
     * @param array $data The data to send with the request
     * @param array $headers The headers to send with the request
     * @return array The response from the service
     */
    public function handleRequest(
        string $endpoint,
        string $method = 'GET',
        array $data = [],
        array $headers = []
    ): array;
    
    /**
     * Register the service with the service registry
     * 
     * @param object $registry The service registry
     * @return string The ID of the registered service
     */
    public function register(object $registry): string;
    
    /**
     * Deregister the service from the service registry
     * 
     * @param object $registry The service registry
     * @param string $serviceId The ID of the registered service
     * @return bool True if the service was deregistered successfully
     */
    public function deregister(object $registry, string $serviceId): bool;
    
    /**
     * Subscribe to events from the message broker
     * 
     * @param object $broker The message broker
     * @return bool True if the subscription was successful
     */
    public function subscribeToEvents(object $broker): bool;
    
    /**
     * Publish an event to the message broker
     * 
     * @param object $broker The message broker
     * @param string $topic The topic to publish to
     * @param array $event The event to publish
     * @return bool True if the event was published successfully
     */
    public function publishEvent(object $broker, string $topic, array $event): bool;
}
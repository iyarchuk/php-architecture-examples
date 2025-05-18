<?php

namespace MicroservicesArchitecture\CircuitBreaker;

/**
 * CircuitBreaker
 * 
 * This interface defines the contract for a circuit breaker.
 * A circuit breaker prevents cascading failures by monitoring service health
 * and automatically stopping calls to failing services.
 */
interface CircuitBreaker
{
    /**
     * Check if a service call is allowed
     * 
     * @param string $serviceName The name of the service
     * @return bool True if the call is allowed, false otherwise
     */
    public function isAllowed(string $serviceName): bool;
    
    /**
     * Record a successful service call
     * 
     * @param string $serviceName The name of the service
     * @return void
     */
    public function recordSuccess(string $serviceName): void;
    
    /**
     * Record a failed service call
     * 
     * @param string $serviceName The name of the service
     * @return void
     */
    public function recordFailure(string $serviceName): void;
    
    /**
     * Get the current state of the circuit breaker for a service
     * 
     * @param string $serviceName The name of the service
     * @return string The current state (e.g., "closed", "open", "half-open")
     */
    public function getState(string $serviceName): string;
    
    /**
     * Reset the circuit breaker for a service
     * 
     * @param string $serviceName The name of the service
     * @return void
     */
    public function reset(string $serviceName): void;
    
    /**
     * Get statistics for a service
     * 
     * @param string $serviceName The name of the service
     * @return array Statistics about the service calls
     */
    public function getStatistics(string $serviceName): array;
}
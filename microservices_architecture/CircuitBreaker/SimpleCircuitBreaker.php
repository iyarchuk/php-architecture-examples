<?php

namespace MicroservicesArchitecture\CircuitBreaker;

/**
 * SimpleCircuitBreaker
 * 
 * This class provides a simple implementation of the CircuitBreaker interface.
 * It monitors service health and automatically stops calls to failing services.
 */
class SimpleCircuitBreaker implements CircuitBreaker
{
    /**
     * Circuit breaker states
     */
    const STATE_CLOSED = 'closed';
    const STATE_OPEN = 'open';
    const STATE_HALF_OPEN = 'half-open';
    
    /**
     * @var array The circuit breakers for each service
     */
    private array $circuits = [];
    
    /**
     * @var int The failure threshold before opening the circuit
     */
    private int $failureThreshold;
    
    /**
     * @var int The reset timeout in seconds
     */
    private int $resetTimeout;
    
    /**
     * @var int The number of successful calls required to close the circuit
     */
    private int $successThreshold;
    
    /**
     * Constructor
     * 
     * @param int $failureThreshold The failure threshold before opening the circuit
     * @param int $resetTimeout The reset timeout in seconds
     * @param int $successThreshold The number of successful calls required to close the circuit
     */
    public function __construct(int $failureThreshold = 5, int $resetTimeout = 30, int $successThreshold = 2)
    {
        $this->failureThreshold = $failureThreshold;
        $this->resetTimeout = $resetTimeout;
        $this->successThreshold = $successThreshold;
    }
    
    /**
     * Check if a service call is allowed
     * 
     * @param string $serviceName The name of the service
     * @return bool True if the call is allowed, false otherwise
     */
    public function isAllowed(string $serviceName): bool
    {
        $this->initializeCircuit($serviceName);
        $circuit = &$this->circuits[$serviceName];
        
        // If the circuit is open, check if the reset timeout has elapsed
        if ($circuit['state'] === self::STATE_OPEN) {
            $elapsedTime = time() - $circuit['lastStateChange'];
            
            // If the reset timeout has elapsed, transition to half-open
            if ($elapsedTime >= $this->resetTimeout) {
                $circuit['state'] = self::STATE_HALF_OPEN;
                $circuit['lastStateChange'] = time();
                $circuit['successCount'] = 0;
            } else {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Record a successful service call
     * 
     * @param string $serviceName The name of the service
     * @return void
     */
    public function recordSuccess(string $serviceName): void
    {
        $this->initializeCircuit($serviceName);
        $circuit = &$this->circuits[$serviceName];
        
        // Reset failure count
        $circuit['failureCount'] = 0;
        $circuit['lastSuccess'] = time();
        $circuit['totalSuccesses']++;
        
        // If the circuit is half-open, increment success count
        if ($circuit['state'] === self::STATE_HALF_OPEN) {
            $circuit['successCount']++;
            
            // If success threshold is reached, close the circuit
            if ($circuit['successCount'] >= $this->successThreshold) {
                $circuit['state'] = self::STATE_CLOSED;
                $circuit['lastStateChange'] = time();
            }
        }
    }
    
    /**
     * Record a failed service call
     * 
     * @param string $serviceName The name of the service
     * @return void
     */
    public function recordFailure(string $serviceName): void
    {
        $this->initializeCircuit($serviceName);
        $circuit = &$this->circuits[$serviceName];
        
        $circuit['failureCount']++;
        $circuit['lastFailure'] = time();
        $circuit['totalFailures']++;
        
        // If the circuit is closed and failure threshold is reached, open the circuit
        if ($circuit['state'] === self::STATE_CLOSED && $circuit['failureCount'] >= $this->failureThreshold) {
            $circuit['state'] = self::STATE_OPEN;
            $circuit['lastStateChange'] = time();
        }
        
        // If the circuit is half-open, open the circuit again
        if ($circuit['state'] === self::STATE_HALF_OPEN) {
            $circuit['state'] = self::STATE_OPEN;
            $circuit['lastStateChange'] = time();
        }
    }
    
    /**
     * Get the current state of the circuit breaker for a service
     * 
     * @param string $serviceName The name of the service
     * @return string The current state (e.g., "closed", "open", "half-open")
     */
    public function getState(string $serviceName): string
    {
        $this->initializeCircuit($serviceName);
        return $this->circuits[$serviceName]['state'];
    }
    
    /**
     * Reset the circuit breaker for a service
     * 
     * @param string $serviceName The name of the service
     * @return void
     */
    public function reset(string $serviceName): void
    {
        $this->circuits[$serviceName] = [
            'state' => self::STATE_CLOSED,
            'failureCount' => 0,
            'successCount' => 0,
            'lastStateChange' => time(),
            'lastSuccess' => null,
            'lastFailure' => null,
            'totalSuccesses' => 0,
            'totalFailures' => 0
        ];
    }
    
    /**
     * Get statistics for a service
     * 
     * @param string $serviceName The name of the service
     * @return array Statistics about the service calls
     */
    public function getStatistics(string $serviceName): array
    {
        $this->initializeCircuit($serviceName);
        $circuit = $this->circuits[$serviceName];
        
        return [
            'state' => $circuit['state'],
            'failureCount' => $circuit['failureCount'],
            'successCount' => $circuit['successCount'],
            'lastStateChange' => $circuit['lastStateChange'],
            'lastSuccess' => $circuit['lastSuccess'],
            'lastFailure' => $circuit['lastFailure'],
            'totalSuccesses' => $circuit['totalSuccesses'],
            'totalFailures' => $circuit['totalFailures'],
            'failureRate' => $this->calculateFailureRate($serviceName)
        ];
    }
    
    /**
     * Initialize the circuit for a service if it doesn't exist
     * 
     * @param string $serviceName The name of the service
     * @return void
     */
    private function initializeCircuit(string $serviceName): void
    {
        if (!isset($this->circuits[$serviceName])) {
            $this->reset($serviceName);
        }
    }
    
    /**
     * Calculate the failure rate for a service
     * 
     * @param string $serviceName The name of the service
     * @return float The failure rate (0-1)
     */
    private function calculateFailureRate(string $serviceName): float
    {
        $circuit = $this->circuits[$serviceName];
        $total = $circuit['totalSuccesses'] + $circuit['totalFailures'];
        
        if ($total === 0) {
            return 0.0;
        }
        
        return $circuit['totalFailures'] / $total;
    }
}
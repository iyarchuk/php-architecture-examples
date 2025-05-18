<?php

namespace ServerlessArchitecture\Events;

/**
 * DatabaseEventTrigger handles database events in a serverless environment.
 * In a serverless architecture, event triggers respond to events from various sources
 * and invoke the appropriate functions.
 */
class DatabaseEventTrigger
{
    /**
     * @var array The configuration for the event trigger
     */
    private $config;
    
    /**
     * Constructor
     *
     * @param array $config The configuration for the event trigger
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'tableName' => '',
            'operation' => '',
            'functionName' => '',
            'batchSize' => 100,
            'enabled' => true
        ], $config);
    }
    
    /**
     * Process a database event
     *
     * @param array $event The event data
     * @return array The result of processing the event
     */
    public function processEvent(array $event): array
    {
        // Check if the trigger is enabled
        if (!$this->config['enabled']) {
            return [
                'success' => false,
                'message' => 'Event trigger is disabled'
            ];
        }
        
        // Check if the event is for the configured table
        if ($event['tableName'] !== $this->config['tableName']) {
            return [
                'success' => false,
                'message' => 'Event is not for the configured table'
            ];
        }
        
        // Check if the event is for the configured operation
        if ($event['operation'] !== $this->config['operation']) {
            return [
                'success' => false,
                'message' => 'Event is not for the configured operation'
            ];
        }
        
        // In a real application, we would invoke the configured function
        // For this example, we'll just return a success response
        return [
            'success' => true,
            'message' => 'Event processed successfully',
            'data' => [
                'tableName' => $event['tableName'],
                'operation' => $event['operation'],
                'records' => $event['records'] ?? [],
                'functionName' => $this->config['functionName'],
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ];
    }
    
    /**
     * Get the configuration for the event trigger
     *
     * @return array The configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }
    
    /**
     * Set the configuration for the event trigger
     *
     * @param array $config The configuration
     * @return DatabaseEventTrigger Returns $this for method chaining
     */
    public function setConfig(array $config): DatabaseEventTrigger
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }
    
    /**
     * Enable the event trigger
     *
     * @return DatabaseEventTrigger Returns $this for method chaining
     */
    public function enable(): DatabaseEventTrigger
    {
        $this->config['enabled'] = true;
        return $this;
    }
    
    /**
     * Disable the event trigger
     *
     * @return DatabaseEventTrigger Returns $this for method chaining
     */
    public function disable(): DatabaseEventTrigger
    {
        $this->config['enabled'] = false;
        return $this;
    }
}
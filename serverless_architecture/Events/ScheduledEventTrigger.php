<?php

namespace ServerlessArchitecture\Events;

/**
 * ScheduledEventTrigger handles scheduled events in a serverless environment.
 * In a serverless architecture, scheduled events can trigger functions at specified times
 * or intervals, similar to cron jobs in traditional systems.
 */
class ScheduledEventTrigger
{
    /**
     * @var array The configuration for the scheduled event
     */
    private $config;
    
    /**
     * Constructor
     *
     * @param array $config The configuration for the scheduled event
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'name' => '',
            'schedule' => '',
            'functionName' => '',
            'enabled' => true,
            'input' => []
        ], $config);
    }
    
    /**
     * Process a scheduled event
     *
     * @return array The result of processing the event
     */
    public function processEvent(): array
    {
        // Check if the trigger is enabled
        if (!$this->config['enabled']) {
            return [
                'success' => false,
                'message' => 'Scheduled event trigger is disabled'
            ];
        }
        
        // In a real application, we would invoke the configured function
        // For this example, we'll just return a success response
        return [
            'success' => true,
            'message' => 'Scheduled event processed successfully',
            'data' => [
                'name' => $this->config['name'],
                'schedule' => $this->config['schedule'],
                'functionName' => $this->config['functionName'],
                'input' => $this->config['input'],
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ];
    }
    
    /**
     * Get the configuration for the scheduled event
     *
     * @return array The configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }
    
    /**
     * Set the configuration for the scheduled event
     *
     * @param array $config The configuration
     * @return ScheduledEventTrigger Returns $this for method chaining
     */
    public function setConfig(array $config): ScheduledEventTrigger
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }
    
    /**
     * Enable the scheduled event
     *
     * @return ScheduledEventTrigger Returns $this for method chaining
     */
    public function enable(): ScheduledEventTrigger
    {
        $this->config['enabled'] = true;
        return $this;
    }
    
    /**
     * Disable the scheduled event
     *
     * @return ScheduledEventTrigger Returns $this for method chaining
     */
    public function disable(): ScheduledEventTrigger
    {
        $this->config['enabled'] = false;
        return $this;
    }
    
    /**
     * Set the input data for the scheduled event
     *
     * @param array $input The input data
     * @return ScheduledEventTrigger Returns $this for method chaining
     */
    public function setInput(array $input): ScheduledEventTrigger
    {
        $this->config['input'] = $input;
        return $this;
    }
    
    /**
     * Set the schedule for the event
     *
     * @param string $schedule The schedule expression (e.g., "cron(0 12 * * ? *)" for daily at noon)
     * @return ScheduledEventTrigger Returns $this for method chaining
     */
    public function setSchedule(string $schedule): ScheduledEventTrigger
    {
        $this->config['schedule'] = $schedule;
        return $this;
    }
}
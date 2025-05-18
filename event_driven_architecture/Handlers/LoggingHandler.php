<?php

namespace EventDrivenArchitecture\Handlers;

/**
 * LoggingHandler
 * 
 * This class is responsible for logging events.
 * In a real application, this would use a logging library or service.
 */
class LoggingHandler
{
    /**
     * @var array Log of events
     */
    private array $logs = [];
    
    /**
     * @var string The log level
     */
    private string $logLevel;
    
    /**
     * @var array Valid log levels
     */
    private const LOG_LEVELS = ['debug', 'info', 'warning', 'error'];
    
    /**
     * Constructor
     * 
     * @param string $logLevel The log level (debug, info, warning, error)
     */
    public function __construct(string $logLevel = 'info')
    {
        if (!in_array($logLevel, self::LOG_LEVELS)) {
            throw new \InvalidArgumentException('Invalid log level');
        }
        
        $this->logLevel = $logLevel;
    }
    
    /**
     * Log an event
     * 
     * @param string $eventName
     * @param \DateTimeImmutable $timestamp
     * @param array $data
     * @return void
     */
    public function logEvent(string $eventName, \DateTimeImmutable $timestamp, array $data): void
    {
        $logEntry = [
            'event' => $eventName,
            'timestamp' => $timestamp,
            'data' => $data,
            'log_level' => $this->determineLogLevel($eventName),
            'logged_at' => new \DateTimeImmutable()
        ];
        
        $this->logs[] = $logEntry;
        
        // In a real application, this would use a logging library or service
        // For example: Monolog, PSR-3 logger, etc.
        
        // For this example, we'll just echo the log entry
        $this->displayLogEntry($logEntry);
    }
    
    /**
     * Determine the log level based on the event name
     * 
     * @param string $eventName
     * @return string
     */
    private function determineLogLevel(string $eventName): string
    {
        // In a real application, this would be more sophisticated
        if (strpos($eventName, 'error') !== false) {
            return 'error';
        } elseif (strpos($eventName, 'warning') !== false) {
            return 'warning';
        } elseif (strpos($eventName, 'deleted') !== false) {
            return 'warning';
        } else {
            return 'info';
        }
    }
    
    /**
     * Display a log entry
     * 
     * @param array $logEntry
     * @return void
     */
    private function displayLogEntry(array $logEntry): void
    {
        // Only display log entries at or above the configured log level
        $logLevelIndex = array_search($logEntry['log_level'], self::LOG_LEVELS);
        $configuredLevelIndex = array_search($this->logLevel, self::LOG_LEVELS);
        
        if ($logLevelIndex < $configuredLevelIndex) {
            return;
        }
        
        $timestamp = $logEntry['timestamp']->format('Y-m-d H:i:s');
        $level = strtoupper($logEntry['log_level']);
        $event = $logEntry['event'];
        $data = json_encode($logEntry['data']);
        
        echo "[$timestamp] [$level] Event: $event, Data: $data\n";
    }
    
    /**
     * Get all logs
     * 
     * @return array
     */
    public function getLogs(): array
    {
        return $this->logs;
    }
    
    /**
     * Get logs filtered by log level
     * 
     * @param string $level
     * @return array
     */
    public function getLogsByLevel(string $level): array
    {
        if (!in_array($level, self::LOG_LEVELS)) {
            throw new \InvalidArgumentException('Invalid log level');
        }
        
        $levelIndex = array_search($level, self::LOG_LEVELS);
        
        return array_filter($this->logs, function ($log) use ($levelIndex) {
            $logLevelIndex = array_search($log['log_level'], self::LOG_LEVELS);
            return $logLevelIndex >= $levelIndex;
        });
    }
    
    /**
     * Clear all logs
     * 
     * @return void
     */
    public function clearLogs(): void
    {
        $this->logs = [];
    }
    
    /**
     * Set the log level
     * 
     * @param string $logLevel
     * @return void
     */
    public function setLogLevel(string $logLevel): void
    {
        if (!in_array($logLevel, self::LOG_LEVELS)) {
            throw new \InvalidArgumentException('Invalid log level');
        }
        
        $this->logLevel = $logLevel;
    }
    
    /**
     * Get the log level
     * 
     * @return string
     */
    public function getLogLevel(): string
    {
        return $this->logLevel;
    }
}
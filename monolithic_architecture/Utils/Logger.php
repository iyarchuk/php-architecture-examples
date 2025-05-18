<?php

namespace MonolithicArchitecture\Utils;

/**
 * Logger utility for logging messages
 */
class Logger
{
    /**
     * Log levels
     */
    const DEBUG = 'DEBUG';
    const INFO = 'INFO';
    const WARNING = 'WARNING';
    const ERROR = 'ERROR';
    const CRITICAL = 'CRITICAL';

    /**
     * Log a debug message
     * 
     * @param string $message Message to log
     * @param array $context Additional context data
     * @return void
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log(self::DEBUG, $message, $context);
    }

    /**
     * Log an info message
     * 
     * @param string $message Message to log
     * @param array $context Additional context data
     * @return void
     */
    public function info(string $message, array $context = []): void
    {
        $this->log(self::INFO, $message, $context);
    }

    /**
     * Log a warning message
     * 
     * @param string $message Message to log
     * @param array $context Additional context data
     * @return void
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log(self::WARNING, $message, $context);
    }

    /**
     * Log an error message
     * 
     * @param string $message Message to log
     * @param array $context Additional context data
     * @return void
     */
    public function error(string $message, array $context = []): void
    {
        $this->log(self::ERROR, $message, $context);
    }

    /**
     * Log a critical message
     * 
     * @param string $message Message to log
     * @param array $context Additional context data
     * @return void
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log(self::CRITICAL, $message, $context);
    }

    /**
     * Log a message with the specified level
     * 
     * @param string $level Log level
     * @param string $message Message to log
     * @param array $context Additional context data
     * @return void
     */
    private function log(string $level, string $message, array $context = []): void
    {
        // In a real application, we would log to a file, database, or external service
        // For this example, we'll just simulate logging
        $timestamp = date('Y-m-d H:i:s');
        $contextString = empty($context) ? '' : ' ' . json_encode($context);
        
        // In a real application, we would write to a log file or send to a logging service
        // For this example, we'll just output to the console if this were run from the command line
        // echo "[$timestamp] [$level] $message$contextString" . PHP_EOL;
        
        // In a real application, we might also store logs in memory for debugging
        $this->storeLog($timestamp, $level, $message, $context);
    }

    /**
     * Store a log entry in memory
     * 
     * @param string $timestamp Log timestamp
     * @param string $level Log level
     * @param string $message Log message
     * @param array $context Additional context data
     * @return void
     */
    private function storeLog(string $timestamp, string $level, string $message, array $context = []): void
    {
        // In a real application, we might store logs in memory for debugging
        // For this example, we'll just simulate storing logs
        static $logs = [];
        
        $logs[] = [
            'timestamp' => $timestamp,
            'level' => $level,
            'message' => $message,
            'context' => $context
        ];
        
        // Limit the number of logs stored in memory
        if (count($logs) > 1000) {
            array_shift($logs);
        }
    }

    /**
     * Get all logs
     * 
     * @return array List of logs
     */
    public function getLogs(): array
    {
        // In a real application, we might retrieve logs from memory or storage
        // For this example, we'll just simulate retrieving logs
        static $logs = [];
        return $logs;
    }

    /**
     * Clear all logs
     * 
     * @return void
     */
    public function clearLogs(): void
    {
        // In a real application, we might clear logs from memory or storage
        // For this example, we'll just simulate clearing logs
        static $logs = [];
        $logs = [];
    }
}
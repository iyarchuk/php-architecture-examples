<?php

namespace ServerlessArchitecture\Config;

/**
 * ServerlessConfig handles configuration for the serverless architecture.
 * In a serverless architecture, configuration is typically stored in environment variables,
 * configuration files, or a combination of both.
 */
class ServerlessConfig
{
    /**
     * @var array The configuration values
     */
    private $config = [];
    
    /**
     * @var ServerlessConfig The singleton instance
     */
    private static $instance;
    
    /**
     * Private constructor to prevent direct instantiation
     *
     * @param array $config The initial configuration values
     */
    private function __construct(array $config = [])
    {
        $this->config = array_merge([
            'app' => [
                'name' => 'ServerlessExample',
                'version' => '1.0.0',
                'environment' => 'development'
            ],
            'api' => [
                'name' => 'ServerlessAPI',
                'version' => 'v1',
                'basePath' => '/api',
                'cors' => true
            ],
            'database' => [
                'tableName' => 'serverless-example-table',
                'region' => 'us-east-1',
                'endpoint' => null
            ],
            'storage' => [
                'bucketName' => 'serverless-example-bucket',
                'region' => 'us-east-1',
                'acl' => 'private'
            ],
            'functions' => [
                'timeout' => 30,
                'memory' => 128,
                'runtime' => 'php-8.1'
            ],
            'events' => [
                'enabled' => true
            ]
        ], $config);
    }
    
    /**
     * Get the singleton instance
     *
     * @param array $config The initial configuration values (only used if instance doesn't exist)
     * @return ServerlessConfig The singleton instance
     */
    public static function getInstance(array $config = []): ServerlessConfig
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        
        return self::$instance;
    }
    
    /**
     * Get a configuration value
     *
     * @param string $key The configuration key (dot notation supported)
     * @param mixed $default The default value if the key doesn't exist
     * @return mixed The configuration value
     */
    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            
            $value = $value[$k];
        }
        
        return $value;
    }
    
    /**
     * Set a configuration value
     *
     * @param string $key The configuration key (dot notation supported)
     * @param mixed $value The configuration value
     * @return ServerlessConfig Returns $this for method chaining
     */
    public function set(string $key, $value): ServerlessConfig
    {
        $keys = explode('.', $key);
        $config = &$this->config;
        
        foreach ($keys as $i => $k) {
            if ($i === count($keys) - 1) {
                $config[$k] = $value;
            } else {
                if (!isset($config[$k]) || !is_array($config[$k])) {
                    $config[$k] = [];
                }
                
                $config = &$config[$k];
            }
        }
        
        return $this;
    }
    
    /**
     * Check if a configuration key exists
     *
     * @param string $key The configuration key (dot notation supported)
     * @return bool True if the key exists
     */
    public function has(string $key): bool
    {
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return false;
            }
            
            $value = $value[$k];
        }
        
        return true;
    }
    
    /**
     * Get all configuration values
     *
     * @return array The configuration values
     */
    public function all(): array
    {
        return $this->config;
    }
    
    /**
     * Load configuration from a file
     *
     * @param string $file The path to the configuration file
     * @return ServerlessConfig Returns $this for method chaining
     */
    public function loadFromFile(string $file): ServerlessConfig
    {
        if (file_exists($file)) {
            $config = require $file;
            
            if (is_array($config)) {
                $this->config = array_merge($this->config, $config);
            }
        }
        
        return $this;
    }
    
    /**
     * Load configuration from environment variables
     *
     * @param string $prefix The prefix for environment variables
     * @return ServerlessConfig Returns $this for method chaining
     */
    public function loadFromEnvironment(string $prefix = 'SERVERLESS_'): ServerlessConfig
    {
        foreach ($_ENV as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $configKey = strtolower(str_replace($prefix, '', $key));
                $configKey = str_replace('_', '.', $configKey);
                
                $this->set($configKey, $value);
            }
        }
        
        return $this;
    }
}
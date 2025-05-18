<?php

namespace ClientServerArchitecture\Shared;

/**
 * Request
 * 
 * This class represents a request from the client to the server.
 * It encapsulates the action to be performed and the data associated with the request.
 */
class Request
{
    /**
     * @var string The action to be performed
     */
    private string $action;
    
    /**
     * @var array The data associated with the request
     */
    private array $data;
    
    /**
     * Constructor
     * 
     * @param string $action The action to be performed
     * @param array $data The data associated with the request
     */
    public function __construct(string $action, array $data = [])
    {
        $this->action = $action;
        $this->data = $data;
    }
    
    /**
     * Get the action
     * 
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }
    
    /**
     * Get the data
     * 
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
    
    /**
     * Convert the request to JSON
     * 
     * @return string
     */
    public function toJson(): string
    {
        return json_encode([
            'action' => $this->action,
            'data' => $this->data
        ]);
    }
    
    /**
     * Create a request from JSON
     * 
     * @param string $json
     * @return Request
     * @throws \InvalidArgumentException
     */
    public static function fromJson(string $json): Request
    {
        $data = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
        }
        
        if (!isset($data['action'])) {
            throw new \InvalidArgumentException('Missing required field: action');
        }
        
        return new self($data['action'], $data['data'] ?? []);
    }
}
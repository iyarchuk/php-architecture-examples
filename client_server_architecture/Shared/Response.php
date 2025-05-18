<?php

namespace ClientServerArchitecture\Shared;

/**
 * Response
 * 
 * This class represents a response from the server to the client.
 * It encapsulates the success status, data, and message associated with the response.
 */
class Response
{
    /**
     * @var bool Whether the request was successful
     */
    private bool $success;
    
    /**
     * @var mixed|null The data associated with the response
     */
    private $data;
    
    /**
     * @var string|null The message associated with the response
     */
    private ?string $message;
    
    /**
     * Constructor
     * 
     * @param bool $success Whether the request was successful
     * @param mixed|null $data The data associated with the response
     * @param string|null $message The message associated with the response
     */
    public function __construct(bool $success, $data = null, ?string $message = null)
    {
        $this->success = $success;
        $this->data = $data;
        $this->message = $message;
    }
    
    /**
     * Check if the request was successful
     * 
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }
    
    /**
     * Get the data
     * 
     * @return mixed|null
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * Get the message
     * 
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }
    
    /**
     * Convert the response to JSON
     * 
     * @return string
     */
    public function toJson(): string
    {
        return json_encode([
            'success' => $this->success,
            'data' => $this->data,
            'message' => $this->message
        ]);
    }
    
    /**
     * Create a response from JSON
     * 
     * @param string $json
     * @return Response
     * @throws \InvalidArgumentException
     */
    public static function fromJson(string $json): Response
    {
        $data = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
        }
        
        if (!isset($data['success'])) {
            throw new \InvalidArgumentException('Missing required field: success');
        }
        
        return new self(
            (bool)$data['success'],
            $data['data'] ?? null,
            $data['message'] ?? null
        );
    }
    
    /**
     * Create a success response
     * 
     * @param mixed|null $data
     * @param string|null $message
     * @return Response
     */
    public static function success($data = null, ?string $message = null): Response
    {
        return new self(true, $data, $message);
    }
    
    /**
     * Create an error response
     * 
     * @param string $message
     * @param mixed|null $data
     * @return Response
     */
    public static function error(string $message, $data = null): Response
    {
        return new self(false, $data, $message);
    }
}
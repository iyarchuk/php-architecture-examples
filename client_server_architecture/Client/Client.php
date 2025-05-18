<?php

namespace ClientServerArchitecture\Client;

use ClientServerArchitecture\Shared\Protocol;
use ClientServerArchitecture\Shared\Request;
use ClientServerArchitecture\Shared\Response;

/**
 * Client
 * 
 * This class represents the client in the client-server architecture.
 * It sends requests to the server and processes responses.
 */
class Client
{
    /**
     * @var object The server instance (for simulation purposes)
     */
    private $server;
    
    /**
     * Constructor
     * 
     * @param object $server The server instance (for simulation purposes)
     */
    public function __construct($server)
    {
        $this->server = $server;
    }
    
    /**
     * Send a request to the server
     * 
     * @param Request $request
     * @return Response
     */
    public function sendRequest(Request $request): Response
    {
        try {
            // Encode the request
            $encodedRequest = Protocol::send($request);
            
            // Send the request to the server and get the encoded response
            // In a real application, this would use sockets, HTTP, or another transport mechanism
            $encodedResponse = $this->server->handleRequest($encodedRequest);
            
            // Decode the response
            $response = Protocol::receive($encodedResponse);
            
            if (!($response instanceof Response)) {
                throw new \InvalidArgumentException('Invalid response format');
            }
            
            return $response;
        } catch (\Exception $e) {
            // Handle any exceptions
            return new Response(false, null, 'Client error: ' . $e->getMessage());
        }
    }
    
    /**
     * Create a user
     * 
     * @param string $name
     * @param string $email
     * @param string $password
     * @return Response
     */
    public function createUser(string $name, string $email, string $password): Response
    {
        $request = new Request('create', [
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);
        
        return $this->sendRequest($request);
    }
    
    /**
     * Get a user by ID
     * 
     * @param int $id
     * @return Response
     */
    public function getUser(int $id): Response
    {
        $request = new Request('get', [
            'id' => $id
        ]);
        
        return $this->sendRequest($request);
    }
    
    /**
     * Get all users
     * 
     * @return Response
     */
    public function getAllUsers(): Response
    {
        $request = new Request('getAll');
        
        return $this->sendRequest($request);
    }
    
    /**
     * Update a user
     * 
     * @param int $id
     * @param array $data
     * @return Response
     */
    public function updateUser(int $id, array $data): Response
    {
        $data['id'] = $id;
        $request = new Request('update', $data);
        
        return $this->sendRequest($request);
    }
    
    /**
     * Delete a user
     * 
     * @param int $id
     * @return Response
     */
    public function deleteUser(int $id): Response
    {
        $request = new Request('delete', [
            'id' => $id
        ]);
        
        return $this->sendRequest($request);
    }
    
    /**
     * Authenticate a user
     * 
     * @param string $email
     * @param string $password
     * @return Response
     */
    public function authenticateUser(string $email, string $password): Response
    {
        $request = new Request('authenticate', [
            'email' => $email,
            'password' => $password
        ]);
        
        return $this->sendRequest($request);
    }
}
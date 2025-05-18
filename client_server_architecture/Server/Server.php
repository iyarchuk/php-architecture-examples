<?php

namespace ClientServerArchitecture\Server;

use ClientServerArchitecture\Server\Controllers\UserController;
use ClientServerArchitecture\Server\Models\UserModel;
use ClientServerArchitecture\Shared\Protocol;
use ClientServerArchitecture\Shared\Request;
use ClientServerArchitecture\Shared\Response;

/**
 * Server
 * 
 * This class represents the server in the client-server architecture.
 * It receives requests, routes them to the appropriate controllers, and returns responses.
 */
class Server
{
    /**
     * @var UserController The user controller
     */
    private UserController $userController;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        // Initialize models
        $userModel = new UserModel();
        
        // Initialize controllers
        $this->userController = new UserController($userModel);
    }
    
    /**
     * Handle a request
     * 
     * @param string $encodedRequest
     * @return string
     */
    public function handleRequest(string $encodedRequest): string
    {
        try {
            // Decode the request
            $request = Protocol::receive($encodedRequest);
            
            if (!($request instanceof Request)) {
                throw new \InvalidArgumentException('Invalid request format');
            }
            
            // Route the request to the appropriate controller
            $response = $this->routeRequest($request);
            
            // Encode and return the response
            return Protocol::send($response);
        } catch (\Exception $e) {
            // Handle any exceptions
            $response = new Response(false, null, 'Server error: ' . $e->getMessage());
            return Protocol::send($response);
        }
    }
    
    /**
     * Route a request to the appropriate controller
     * 
     * @param Request $request
     * @return Response
     */
    private function routeRequest(Request $request): Response
    {
        $action = $request->getAction();
        
        // Route user-related actions to the user controller
        if (in_array($action, ['create', 'get', 'getAll', 'update', 'delete', 'authenticate'])) {
            return $this->userController->handleRequest($request);
        }
        
        // Handle unknown actions
        return new Response(false, null, "Unknown action: $action");
    }
    
    /**
     * Start the server
     * 
     * In a real application, this would start a socket server or HTTP server.
     * For this example, we're just returning a message.
     * 
     * @return string
     */
    public function start(): string
    {
        return "Server started and listening for requests...";
    }
    
    /**
     * Stop the server
     * 
     * In a real application, this would stop the socket server or HTTP server.
     * For this example, we're just returning a message.
     * 
     * @return string
     */
    public function stop(): string
    {
        return "Server stopped.";
    }
}
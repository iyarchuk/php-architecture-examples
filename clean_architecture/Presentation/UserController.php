<?php

namespace CleanArchitecture\Presentation;

use CleanArchitecture\Application\CreateUserRequest;
use CleanArchitecture\Application\CreateUserUseCase;
use CleanArchitecture\Application\GetUserRequest;
use CleanArchitecture\Application\GetUserUseCase;

/**
 * User Controller
 * 
 * This class is part of the Presentation layer and handles HTTP requests and responses.
 * It uses the use cases from the Application layer to perform operations.
 * In a real application, this would typically be a controller in a web framework.
 */
class UserController
{
    private CreateUserUseCase $createUserUseCase;
    private GetUserUseCase $getUserUseCase;

    /**
     * Constructor
     * 
     * @param CreateUserUseCase $createUserUseCase
     * @param GetUserUseCase $getUserUseCase
     */
    public function __construct(CreateUserUseCase $createUserUseCase, GetUserUseCase $getUserUseCase)
    {
        $this->createUserUseCase = $createUserUseCase;
        $this->getUserUseCase = $getUserUseCase;
    }

    /**
     * Create a new user
     * 
     * @param array $requestData
     * @return array
     */
    public function createUser(array $requestData): array
    {
        // Validate request data
        if (!isset($requestData['name']) || !isset($requestData['email']) || !isset($requestData['password'])) {
            return [
                'success' => false,
                'message' => 'Missing required fields: name, email, password'
            ];
        }

        // Create a request DTO
        $request = new CreateUserRequest(
            $requestData['name'],
            $requestData['email'],
            $requestData['password']
        );

        // Execute the use case
        $response = $this->createUserUseCase->execute($request);

        // Return a response
        if ($response->success) {
            return [
                'success' => true,
                'message' => 'User created successfully',
                'data' => [
                    'id' => $response->user->getId(),
                    'name' => $response->user->getName(),
                    'email' => $response->user->getEmail()
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => $response->errorMessage
            ];
        }
    }

    /**
     * Get a user by ID
     * 
     * @param int $userId
     * @return array
     */
    public function getUser(int $userId): array
    {
        // Create a request DTO
        $request = new GetUserRequest($userId);

        // Execute the use case
        $response = $this->getUserUseCase->execute($request);

        // Return a response
        if ($response->success) {
            return [
                'success' => true,
                'data' => [
                    'id' => $response->user->getId(),
                    'name' => $response->user->getName(),
                    'email' => $response->user->getEmail()
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => $response->errorMessage
            ];
        }
    }
}
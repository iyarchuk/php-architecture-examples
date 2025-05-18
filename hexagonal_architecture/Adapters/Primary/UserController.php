<?php

namespace HexagonalArchitecture\Adapters\Primary;

use HexagonalArchitecture\Ports\Primary\UserServicePort;

/**
 * User Controller (Primary/Driving Adapter)
 * 
 * This class is a primary adapter that uses the UserServicePort to interact with the domain.
 * It handles HTTP requests and responses for user operations.
 * In a real application, this would typically be a controller in a web framework.
 * 
 * This is a "Primary" or "Driving" adapter because it drives the domain
 * (it calls methods on the domain through the port).
 */
class UserController
{
    private UserServicePort $userService;

    /**
     * Constructor
     * 
     * @param UserServicePort $userService
     */
    public function __construct(UserServicePort $userService)
    {
        $this->userService = $userService;
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

        try {
            // Call the domain service
            $user = $this->userService->createUser(
                $requestData['name'],
                $requestData['email'],
                $requestData['password']
            );

            // Return a response
            return [
                'success' => true,
                'message' => 'User created successfully',
                'data' => [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail()
                ]
            ];
        } catch (\InvalidArgumentException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
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
        // Call the domain service
        $user = $this->userService->getUserById($userId);

        // Return a response
        if ($user !== null) {
            return [
                'success' => true,
                'data' => [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail()
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }
    }

    /**
     * Get all users
     * 
     * @return array
     */
    public function getAllUsers(): array
    {
        // Call the domain service
        $users = $this->userService->getAllUsers();

        // Transform users to a simple array
        $usersArray = [];
        foreach ($users as $user) {
            $usersArray[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail()
            ];
        }

        // Return a response
        return [
            'success' => true,
            'data' => $usersArray
        ];
    }

    /**
     * Delete a user
     * 
     * @param int $userId
     * @return array
     */
    public function deleteUser(int $userId): array
    {
        // Call the domain service
        $user = $this->userService->getUserById($userId);

        if ($user === null) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        $result = $this->userService->deleteUser($user);

        // Return a response
        if ($result) {
            return [
                'success' => true,
                'message' => 'User deleted successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to delete user'
            ];
        }
    }
}
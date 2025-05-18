<?php

namespace LayeredArchitecture\Presentation;

use LayeredArchitecture\Business\UserService;
use LayeredArchitecture\Model\User;

/**
 * User Controller
 * 
 * This class is part of the Presentation layer and handles user requests and responses.
 * It uses the Business layer to perform operations.
 */
class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
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
            // Call the business layer
            $user = $this->userService->createUser(
                $requestData['name'],
                $requestData['email'],
                $requestData['password']
            );

            // Return a response
            return [
                'success' => true,
                'message' => 'User created successfully',
                'data' => $this->formatUser($user)
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
        // Call the business layer
        $user = $this->userService->getUserById($userId);

        // Return a response
        if ($user !== null) {
            return [
                'success' => true,
                'data' => $this->formatUser($user)
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
        // Call the business layer
        $users = $this->userService->getAllUsers();

        // Transform users to a simple array
        $usersArray = [];
        foreach ($users as $user) {
            $usersArray[] = $this->formatUser($user);
        }

        // Return a response
        return [
            'success' => true,
            'data' => $usersArray
        ];
    }

    /**
     * Update a user
     * 
     * @param int $userId
     * @param array $requestData
     * @return array
     */
    public function updateUser(int $userId, array $requestData): array
    {
        // Call the business layer to get the user
        $user = $this->userService->getUserById($userId);

        if ($user === null) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        try {
            // Call the business layer to update the user
            $updatedUser = $this->userService->updateUser($user, $requestData);

            // Return a response
            return [
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $this->formatUser($updatedUser)
            ];
        } catch (\InvalidArgumentException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete a user
     * 
     * @param int $userId
     * @return array
     */
    public function deleteUser(int $userId): array
    {
        // Call the business layer to get the user
        $user = $this->userService->getUserById($userId);

        if ($user === null) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        // Call the business layer to delete the user
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

    /**
     * Authenticate a user
     * 
     * @param array $requestData
     * @return array
     */
    public function authenticateUser(array $requestData): array
    {
        // Validate request data
        if (!isset($requestData['email']) || !isset($requestData['password'])) {
            return [
                'success' => false,
                'message' => 'Missing required fields: email, password'
            ];
        }

        // Call the business layer
        $user = $this->userService->authenticateUser(
            $requestData['email'],
            $requestData['password']
        );

        // Return a response
        if ($user !== null) {
            return [
                'success' => true,
                'message' => 'Authentication successful',
                'data' => $this->formatUser($user)
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }
    }

    /**
     * Format a user object for response
     * 
     * @param User $user
     * @return array
     */
    private function formatUser(User $user): array
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s')
        ];
    }
}
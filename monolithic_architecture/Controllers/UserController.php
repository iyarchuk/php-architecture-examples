<?php

namespace MonolithicArchitecture\Controllers;

use MonolithicArchitecture\Services\UserService;

/**
 * UserController handles user-related HTTP requests
 */
class UserController
{
    private $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Handle user login
     * 
     * @param array $data Login credentials
     * @return array Response with user data and token
     */
    public function login(array $data): array
    {
        // Validate input
        if (!isset($data['email']) || !isset($data['password'])) {
            return [
                'status' => 'error',
                'message' => 'Email and password are required'
            ];
        }

        // Call the service to authenticate the user
        return $this->userService->authenticate($data['email'], $data['password']);
    }

    /**
     * Get user profile
     * 
     * @param int $userId User ID
     * @param array $headers Request headers
     * @return array Response with user profile data
     */
    public function getProfile(int $userId, array $headers): array
    {
        // Validate authentication
        if (!$this->isAuthenticated($headers)) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized'
            ];
        }

        // Call the service to get the user profile
        return $this->userService->getProfile($userId);
    }

    /**
     * Update user profile
     * 
     * @param int $userId User ID
     * @param array $data Profile data to update
     * @param array $headers Request headers
     * @return array Response with updated user profile data
     */
    public function updateProfile(int $userId, array $data, array $headers): array
    {
        // Validate authentication
        if (!$this->isAuthenticated($headers)) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized'
            ];
        }

        // Call the service to update the user profile
        return $this->userService->updateProfile($userId, $data);
    }

    /**
     * Check if the request is authenticated
     * 
     * @param array $headers Request headers
     * @return bool True if authenticated, false otherwise
     */
    private function isAuthenticated(array $headers): bool
    {
        // Check if the Authorization header is present
        if (!isset($headers['Authorization'])) {
            return false;
        }

        // In a real application, we would validate the token
        // For this example, we'll just check if it starts with "Bearer "
        return strpos($headers['Authorization'], 'Bearer ') === 0;
    }
}
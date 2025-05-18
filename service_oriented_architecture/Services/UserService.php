<?php

namespace ServiceOrientedArchitecture\Services;

use ServiceOrientedArchitecture\Contracts\UserServiceInterface;

/**
 * UserService implements the UserServiceInterface.
 * In a Service-Oriented Architecture, services are independent units of functionality
 * that are accessible over a network.
 */
class UserService implements UserServiceInterface
{
    /**
     * @var array In-memory user storage for this example
     */
    private $users = [];
    
    /**
     * Constructor
     */
    public function __construct()
    {
        // Initialize with some sample users
        $this->users = [
            '1' => [
                'id' => '1',
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'created_at' => '2023-01-01 00:00:00'
            ],
            '2' => [
                'id' => '2',
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'created_at' => '2023-01-02 00:00:00'
            ]
        ];
    }
    
    /**
     * Create a new user
     *
     * @param array $userData The user data
     * @return array The result of the operation
     */
    public function createUser(array $userData): array
    {
        // Validate user data
        if (empty($userData['name']) || empty($userData['email'])) {
            return [
                'success' => false,
                'message' => 'Name and email are required'
            ];
        }
        
        // Check if email is already in use
        foreach ($this->users as $user) {
            if ($user['email'] === $userData['email']) {
                return [
                    'success' => false,
                    'message' => 'Email is already in use'
                ];
            }
        }
        
        // Generate a new user ID
        $userId = (string) (count($this->users) + 1);
        
        // Create the user
        $this->users[$userId] = [
            'id' => $userId,
            'name' => $userData['name'],
            'email' => $userData['email'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return [
            'success' => true,
            'message' => 'User created successfully',
            'data' => $this->users[$userId]
        ];
    }
    
    /**
     * Get a user by ID
     *
     * @param string $userId The user ID
     * @return array The result of the operation
     */
    public function getUser(string $userId): array
    {
        // Check if user exists
        if (!isset($this->users[$userId])) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }
        
        return [
            'success' => true,
            'data' => $this->users[$userId]
        ];
    }
    
    /**
     * Update a user
     *
     * @param string $userId The user ID
     * @param array $userData The user data to update
     * @return array The result of the operation
     */
    public function updateUser(string $userId, array $userData): array
    {
        // Check if user exists
        if (!isset($this->users[$userId])) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }
        
        // Update user data
        if (isset($userData['name'])) {
            $this->users[$userId]['name'] = $userData['name'];
        }
        
        if (isset($userData['email'])) {
            // Check if email is already in use by another user
            foreach ($this->users as $id => $user) {
                if ($id !== $userId && $user['email'] === $userData['email']) {
                    return [
                        'success' => false,
                        'message' => 'Email is already in use'
                    ];
                }
            }
            
            $this->users[$userId]['email'] = $userData['email'];
        }
        
        return [
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $this->users[$userId]
        ];
    }
    
    /**
     * Delete a user
     *
     * @param string $userId The user ID
     * @return array The result of the operation
     */
    public function deleteUser(string $userId): array
    {
        // Check if user exists
        if (!isset($this->users[$userId])) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }
        
        // Delete the user
        $user = $this->users[$userId];
        unset($this->users[$userId]);
        
        return [
            'success' => true,
            'message' => 'User deleted successfully',
            'data' => $user
        ];
    }
    
    /**
     * List all users
     *
     * @param array $filters Optional filters
     * @return array The result of the operation
     */
    public function listUsers(array $filters = []): array
    {
        $users = $this->users;
        
        // Apply filters if any
        if (!empty($filters)) {
            $users = array_filter($users, function ($user) use ($filters) {
                foreach ($filters as $key => $value) {
                    if (!isset($user[$key]) || $user[$key] !== $value) {
                        return false;
                    }
                }
                return true;
            });
        }
        
        return [
            'success' => true,
            'data' => array_values($users)
        ];
    }
}
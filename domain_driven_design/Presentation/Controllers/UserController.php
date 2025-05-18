<?php

namespace DomainDrivenDesign\Presentation\Controllers;

use DomainDrivenDesign\Application\Services\UserApplicationService;

/**
 * UserController
 * 
 * This class represents a controller for user-related HTTP requests.
 * In DDD, controllers are part of the presentation layer and interact with the application layer.
 */
class UserController
{
    /**
     * @var UserApplicationService The user application service
     */
    private UserApplicationService $userApplicationService;
    
    /**
     * Constructor
     * 
     * @param UserApplicationService $userApplicationService
     */
    public function __construct(UserApplicationService $userApplicationService)
    {
        $this->userApplicationService = $userApplicationService;
    }
    
    /**
     * Register a new user
     * 
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        try {
            // Validate input
            if (!isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
                return [
                    'success' => false,
                    'message' => 'Missing required fields: name, email, password',
                    'data' => null
                ];
            }
            
            // Register user
            $userDTO = $this->userApplicationService->registerUser(
                $data['name'],
                $data['email'],
                $data['password']
            );
            
            return [
                'success' => true,
                'message' => 'User registered successfully',
                'data' => $userDTO->toArray()
            ];
        } catch (\InvalidArgumentException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while registering the user',
                'data' => null
            ];
        }
    }
    
    /**
     * Get a user by ID
     * 
     * @param string $id
     * @return array
     */
    public function getUser(string $id): array
    {
        try {
            $userDTO = $this->userApplicationService->getUserById($id);
            
            if (!$userDTO) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                    'data' => null
                ];
            }
            
            return [
                'success' => true,
                'message' => 'User retrieved successfully',
                'data' => $userDTO->toArray()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while retrieving the user',
                'data' => null
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
        try {
            $userDTOs = $this->userApplicationService->getAllUsers();
            
            $users = [];
            foreach ($userDTOs as $userDTO) {
                $users[] = $userDTO->toArray();
            }
            
            return [
                'success' => true,
                'message' => 'Users retrieved successfully',
                'data' => $users
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while retrieving users',
                'data' => null
            ];
        }
    }
    
    /**
     * Update a user
     * 
     * @param string $id
     * @param array $data
     * @return array
     */
    public function updateUser(string $id, array $data): array
    {
        try {
            $userDTO = $this->userApplicationService->updateUser($id, $data);
            
            if (!$userDTO) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                    'data' => null
                ];
            }
            
            return [
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $userDTO->toArray()
            ];
        } catch (\InvalidArgumentException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while updating the user',
                'data' => null
            ];
        }
    }
    
    /**
     * Delete a user
     * 
     * @param string $id
     * @return array
     */
    public function deleteUser(string $id): array
    {
        try {
            $result = $this->userApplicationService->deleteUser($id);
            
            if (!$result) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                    'data' => null
                ];
            }
            
            return [
                'success' => true,
                'message' => 'User deleted successfully',
                'data' => null
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while deleting the user',
                'data' => null
            ];
        }
    }
    
    /**
     * Authenticate a user
     * 
     * @param array $data
     * @return array
     */
    public function authenticate(array $data): array
    {
        try {
            // Validate input
            if (!isset($data['email']) || !isset($data['password'])) {
                return [
                    'success' => false,
                    'message' => 'Missing required fields: email, password',
                    'data' => null
                ];
            }
            
            // Authenticate user
            $userDTO = $this->userApplicationService->authenticateUser(
                $data['email'],
                $data['password']
            );
            
            if (!$userDTO) {
                return [
                    'success' => false,
                    'message' => 'Invalid email or password',
                    'data' => null
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Authentication successful',
                'data' => $userDTO->toArray()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred during authentication',
                'data' => null
            ];
        }
    }
}
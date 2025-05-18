<?php

namespace ServiceOrientedArchitecture\Contracts;

/**
 * UserServiceInterface defines the contract for the User Service.
 * In a Service-Oriented Architecture, service contracts define the operations
 * that services can perform and the data they exchange.
 */
interface UserServiceInterface
{
    /**
     * Create a new user
     *
     * @param array $userData The user data
     * @return array The result of the operation
     */
    public function createUser(array $userData): array;
    
    /**
     * Get a user by ID
     *
     * @param string $userId The user ID
     * @return array The result of the operation
     */
    public function getUser(string $userId): array;
    
    /**
     * Update a user
     *
     * @param string $userId The user ID
     * @param array $userData The user data to update
     * @return array The result of the operation
     */
    public function updateUser(string $userId, array $userData): array;
    
    /**
     * Delete a user
     *
     * @param string $userId The user ID
     * @return array The result of the operation
     */
    public function deleteUser(string $userId): array;
    
    /**
     * List all users
     *
     * @param array $filters Optional filters
     * @return array The result of the operation
     */
    public function listUsers(array $filters = []): array;
}
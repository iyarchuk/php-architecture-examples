<?php

namespace HexagonalArchitecture\Ports\Primary;

use HexagonalArchitecture\Domain\User;

/**
 * User Service Port (Primary/Driving Port)
 * 
 * This interface defines how external systems interact with the domain
 * for user operations. It is implemented by the domain service.
 * 
 * This is a "Primary" or "Driving" port because it is used by external systems
 * to drive the domain (external systems call methods on this interface).
 */
interface UserServicePort
{
    /**
     * Create a new user
     * 
     * @param string $name
     * @param string $email
     * @param string $password
     * @return User
     * @throws \InvalidArgumentException
     */
    public function createUser(string $name, string $email, string $password): User;

    /**
     * Get a user by ID
     * 
     * @param int $id
     * @return User|null
     */
    public function getUserById(int $id): ?User;

    /**
     * Get a user by email
     * 
     * @param string $email
     * @return User|null
     */
    public function getUserByEmail(string $email): ?User;

    /**
     * Get all users
     * 
     * @return User[]
     */
    public function getAllUsers(): array;

    /**
     * Delete a user
     * 
     * @param User $user
     * @return bool
     */
    public function deleteUser(User $user): bool;
}
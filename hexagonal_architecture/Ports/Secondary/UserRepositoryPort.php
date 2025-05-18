<?php

namespace HexagonalArchitecture\Ports\Secondary;

use HexagonalArchitecture\Domain\User;

/**
 * User Repository Port (Secondary/Driven Port)
 * 
 * This interface defines how the domain interacts with external systems
 * for user data persistence. It is implemented by adapters in the infrastructure layer.
 * 
 * This is a "Secondary" or "Driven" port because it is used by the domain
 * to drive external systems (the domain calls methods on this interface).
 */
interface UserRepositoryPort
{
    /**
     * Find a user by their ID
     * 
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;
    
    /**
     * Find a user by their email
     * 
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;
    
    /**
     * Save a user to the repository
     * 
     * @param User $user
     * @return User
     */
    public function save(User $user): User;
    
    /**
     * Delete a user from the repository
     * 
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool;
    
    /**
     * Get all users from the repository
     * 
     * @return User[]
     */
    public function findAll(): array;
}
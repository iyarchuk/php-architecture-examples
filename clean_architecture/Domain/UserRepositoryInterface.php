<?php

namespace CleanArchitecture\Domain;

/**
 * User Repository Interface
 * 
 * This interface defines the contract for accessing user data.
 * It belongs to the domain layer but will be implemented in the infrastructure layer.
 * This follows the Dependency Inversion Principle - high-level modules should not depend on low-level modules.
 * Both should depend on abstractions.
 */
interface UserRepositoryInterface
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
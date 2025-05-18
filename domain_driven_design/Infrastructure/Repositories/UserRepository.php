<?php

namespace DomainDrivenDesign\Infrastructure\Repositories;

use DomainDrivenDesign\Domain\Entities\User;
use DomainDrivenDesign\Domain\ValueObjects\Email;
use DomainDrivenDesign\Domain\ValueObjects\UserId;

/**
 * UserRepository Interface
 * 
 * This interface defines the contract for user persistence.
 * In DDD, repositories provide methods for retrieving and storing domain objects.
 */
interface UserRepository
{
    /**
     * Save a user
     * 
     * @param User $user
     * @return void
     */
    public function save(User $user): void;
    
    /**
     * Find a user by ID
     * 
     * @param UserId $id
     * @return User|null
     */
    public function findById(UserId $id): ?User;
    
    /**
     * Find a user by email
     * 
     * @param Email $email
     * @return User|null
     */
    public function findByEmail(Email $email): ?User;
    
    /**
     * Find all users
     * 
     * @return array
     */
    public function findAll(): array;
    
    /**
     * Remove a user
     * 
     * @param UserId $id
     * @return bool
     */
    public function remove(UserId $id): bool;
    
    /**
     * Check if a user exists by ID
     * 
     * @param UserId $id
     * @return bool
     */
    public function exists(UserId $id): bool;
    
    /**
     * Check if a user exists by email
     * 
     * @param Email $email
     * @param UserId|null $excludeId
     * @return bool
     */
    public function existsByEmail(Email $email, ?UserId $excludeId = null): bool;
}
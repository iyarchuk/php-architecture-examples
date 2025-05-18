<?php

namespace DomainDrivenDesign\Infrastructure\Persistence;

use DomainDrivenDesign\Domain\Entities\User;
use DomainDrivenDesign\Domain\ValueObjects\Email;
use DomainDrivenDesign\Domain\ValueObjects\UserId;
use DomainDrivenDesign\Infrastructure\Repositories\UserRepository;

/**
 * InMemoryUserRepository
 * 
 * This class implements the UserRepository interface using in-memory storage.
 * In a real application, this would be replaced with a database implementation.
 */
class InMemoryUserRepository implements UserRepository
{
    /**
     * @var array In-memory storage for users
     */
    private array $users = [];
    
    /**
     * Save a user
     * 
     * @param User $user
     * @return void
     */
    public function save(User $user): void
    {
        $this->users[$user->getId()->toString()] = $user;
    }
    
    /**
     * Find a user by ID
     * 
     * @param UserId $id
     * @return User|null
     */
    public function findById(UserId $id): ?User
    {
        return $this->users[$id->toString()] ?? null;
    }
    
    /**
     * Find a user by email
     * 
     * @param Email $email
     * @return User|null
     */
    public function findByEmail(Email $email): ?User
    {
        foreach ($this->users as $user) {
            if ($user->getEmail()->equals($email)) {
                return $user;
            }
        }
        
        return null;
    }
    
    /**
     * Find all users
     * 
     * @return array
     */
    public function findAll(): array
    {
        return array_values($this->users);
    }
    
    /**
     * Remove a user
     * 
     * @param UserId $id
     * @return bool
     */
    public function remove(UserId $id): bool
    {
        if (isset($this->users[$id->toString()])) {
            unset($this->users[$id->toString()]);
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if a user exists by ID
     * 
     * @param UserId $id
     * @return bool
     */
    public function exists(UserId $id): bool
    {
        return isset($this->users[$id->toString()]);
    }
    
    /**
     * Check if a user exists by email
     * 
     * @param Email $email
     * @param UserId|null $excludeId
     * @return bool
     */
    public function existsByEmail(Email $email, ?UserId $excludeId = null): bool
    {
        foreach ($this->users as $user) {
            if ($user->getEmail()->equals($email)) {
                // If we're excluding a user ID (e.g., when updating a user), check if it's not the same user
                if ($excludeId !== null && $user->getId()->equals($excludeId)) {
                    continue;
                }
                
                return true;
            }
        }
        
        return false;
    }
}
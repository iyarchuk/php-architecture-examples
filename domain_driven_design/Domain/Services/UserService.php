<?php

namespace DomainDrivenDesign\Domain\Services;

use DomainDrivenDesign\Domain\Entities\User;
use DomainDrivenDesign\Domain\ValueObjects\Email;
use DomainDrivenDesign\Domain\ValueObjects\UserId;
use DomainDrivenDesign\Infrastructure\Repositories\UserRepository;

/**
 * UserService
 * 
 * This class represents a domain service for user-related operations.
 * In DDD, domain services contain domain logic that doesn't naturally fit within an entity or value object.
 */
class UserService
{
    /**
     * @var UserRepository The user repository
     */
    private UserRepository $userRepository;

    /**
     * Constructor
     * 
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Check if an email is already in use
     * 
     * @param Email $email
     * @param UserId|null $excludeUserId
     * @return bool
     */
    public function isEmailAlreadyInUse(Email $email, ?UserId $excludeUserId = null): bool
    {
        return $this->userRepository->existsByEmail($email, $excludeUserId);
    }

    /**
     * Add a user to the repository
     * 
     * @param User $user
     * @return void
     */
    public function addUser(User $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * Get a user by ID
     * 
     * @param UserId $userId
     * @return User|null
     */
    public function getUserById(UserId $userId): ?User
    {
        return $this->userRepository->findById($userId);
    }

    /**
     * Get a user by email
     * 
     * @param Email $email
     * @return User|null
     */
    public function getUserByEmail(Email $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * Get all users
     * 
     * @return array
     */
    public function getAllUsers(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * Remove a user
     * 
     * @param UserId $userId
     * @return bool
     */
    public function removeUser(UserId $userId): bool
    {
        return $this->userRepository->remove($userId);
    }
}

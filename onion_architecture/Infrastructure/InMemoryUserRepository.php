<?php

namespace OnionArchitecture\Infrastructure;

use OnionArchitecture\Domain\User;
use OnionArchitecture\Domain\UserRepository;

/**
 * InMemoryUserRepository in the Infrastructure layer
 * Implements the UserRepository interface from the Domain layer
 * This is a simple in-memory implementation for demonstration purposes
 */
class InMemoryUserRepository implements UserRepository
{
    private $users = [];

    /**
     * Save a user to the repository
     *
     * @param User $user The user to save
     * @return bool True if the user was saved successfully, false otherwise
     */
    public function save(User $user): bool
    {
        $this->users[$user->getId()] = $user;
        return true;
    }

    /**
     * Find a user by ID
     *
     * @param string $id The ID of the user to find
     * @return User|null The user if found, null otherwise
     */
    public function findById(string $id): ?User
    {
        return $this->users[$id] ?? null;
    }

    /**
     * Find a user by email
     *
     * @param string $email The email of the user to find
     * @return User|null The user if found, null otherwise
     */
    public function findByEmail(string $email): ?User
    {
        foreach ($this->users as $user) {
            if ($user->getEmail() === $email) {
                return $user;
            }
        }
        return null;
    }
}
<?php

namespace HexagonalArchitecture\Adapters\Secondary;

use HexagonalArchitecture\Domain\User;
use HexagonalArchitecture\Ports\Secondary\UserRepositoryPort;

/**
 * In-Memory User Repository (Secondary/Driven Adapter)
 * 
 * This class is a concrete implementation of the UserRepositoryPort.
 * It stores users in memory (in an array) for simplicity.
 * In a real application, this would typically be a database repository.
 * 
 * This is a "Secondary" or "Driven" adapter because it is driven by the domain
 * (the domain calls methods on this adapter through the port).
 */
class InMemoryUserRepository implements UserRepositoryPort
{
    /**
     * @var User[] Array of users
     */
    private array $users = [];

    /**
     * @var int Auto-increment ID
     */
    private int $nextId = 1;

    /**
     * Find a user by their ID
     * 
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        foreach ($this->users as $user) {
            if ($user->getId() === $id) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Find a user by their email
     * 
     * @param string $email
     * @return User|null
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

    /**
     * Save a user to the repository
     * 
     * @param User $user
     * @return User
     */
    public function save(User $user): User
    {
        // If the user doesn't have an ID, assign one
        if (empty($user->getId())) {
            $user->setId($this->nextId++);
        }

        // Store the user in the array
        $this->users[$user->getId()] = $user;

        return $user;
    }

    /**
     * Delete a user from the repository
     * 
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        if (isset($this->users[$user->getId()])) {
            unset($this->users[$user->getId()]);
            return true;
        }

        return false;
    }

    /**
     * Get all users from the repository
     * 
     * @return User[]
     */
    public function findAll(): array
    {
        return array_values($this->users);
    }
}
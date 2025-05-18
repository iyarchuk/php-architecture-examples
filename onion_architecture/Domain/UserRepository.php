<?php

namespace OnionArchitecture\Domain;

/**
 * UserRepository interface in the Domain layer (Core)
 * This interface defines the contract for user persistence
 * It will be implemented in the Infrastructure layer
 */
interface UserRepository
{
    /**
     * Save a user to the repository
     *
     * @param User $user The user to save
     * @return bool True if the user was saved successfully, false otherwise
     */
    public function save(User $user): bool;

    /**
     * Find a user by ID
     *
     * @param string $id The ID of the user to find
     * @return User|null The user if found, null otherwise
     */
    public function findById(string $id): ?User;

    /**
     * Find a user by email
     *
     * @param string $email The email of the user to find
     * @return User|null The user if found, null otherwise
     */
    public function findByEmail(string $email): ?User;
}
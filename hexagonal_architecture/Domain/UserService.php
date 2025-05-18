<?php

namespace HexagonalArchitecture\Domain;

use HexagonalArchitecture\Ports\Primary\UserServicePort;
use HexagonalArchitecture\Ports\Secondary\UserRepositoryPort;

/**
 * User Service
 * 
 * This service contains the core business logic for user operations.
 * It uses the Secondary Ports to interact with external systems.
 * It implements the Primary Port to expose its functionality to external systems.
 */
class UserService implements UserServicePort
{
    private UserRepositoryPort $userRepository;

    /**
     * Constructor
     * 
     * @param UserRepositoryPort $userRepository
     */
    public function __construct(UserRepositoryPort $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Create a new user
     * 
     * @param string $name
     * @param string $email
     * @param string $password
     * @return User
     * @throws \InvalidArgumentException
     */
    public function createUser(string $name, string $email, string $password): User
    {
        // Check if user with the same email already exists
        $existingUser = $this->userRepository->findByEmail($email);
        if ($existingUser !== null) {
            throw new \InvalidArgumentException('User with this email already exists');
        }

        // Create a new user entity
        $user = new User($name, $email, $password);

        // Save the user to the repository
        return $this->userRepository->save($user);
    }

    /**
     * Get a user by ID
     * 
     * @param int $id
     * @return User|null
     */
    public function getUserById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Get a user by email
     * 
     * @param string $email
     * @return User|null
     */
    public function getUserByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * Get all users
     * 
     * @return User[]
     */
    public function getAllUsers(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * Delete a user
     * 
     * @param User $user
     * @return bool
     */
    public function deleteUser(User $user): bool
    {
        return $this->userRepository->delete($user);
    }
}

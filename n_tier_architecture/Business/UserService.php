<?php

namespace NTierArchitecture\Business;

use NTierArchitecture\Model\User;
use NTierArchitecture\Data\UserRepository;

/**
 * User Service
 * 
 * This class is part of the Business tier and contains the business logic for user operations.
 * It uses the Data tier to access data.
 */
class UserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
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
     * Update a user
     * 
     * @param User $user
     * @param array $data
     * @return User
     * @throws \InvalidArgumentException
     */
    public function updateUser(User $user, array $data): User
    {
        // Update user properties if provided
        if (isset($data['name'])) {
            $user->setName($data['name']);
        }

        if (isset($data['email'])) {
            // Check if the new email is already taken by another user
            $existingUser = $this->userRepository->findByEmail($data['email']);
            if ($existingUser !== null && $existingUser->getId() !== $user->getId()) {
                throw new \InvalidArgumentException('Email is already taken by another user');
            }
            $user->setEmail($data['email']);
        }

        if (isset($data['password'])) {
            $user->setPassword($data['password']);
        }

        // Save the updated user
        return $this->userRepository->save($user);
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

    /**
     * Authenticate a user
     * 
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function authenticateUser(string $email, string $password): ?User
    {
        $user = $this->userRepository->findByEmail($email);
        if ($user === null) {
            return null;
        }

        if (!$user->verifyPassword($password)) {
            return null;
        }

        return $user;
    }
}
<?php

namespace OnionArchitecture\Domain;

/**
 * UserService in the Domain layer (Core)
 * Contains domain logic related to users
 */
class UserService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Create a new user
     *
     * @param string $name The name of the user
     * @param string $email The email of the user
     * @param string $password The password of the user
     * @return array Result of the operation
     */
    public function createUser(string $name, string $email, string $password): array
    {
        // Check if user with this email already exists
        $existingUser = $this->userRepository->findByEmail($email);
        if ($existingUser) {
            return [
                'success' => false,
                'message' => 'User with this email already exists'
            ];
        }

        // Create and validate the user
        $user = new User($name, $email, $password);
        if (!$user->isValid()) {
            return [
                'success' => false,
                'message' => 'Invalid user data'
            ];
        }

        // Save the user
        $result = $this->userRepository->save($user);
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Failed to save user'
            ];
        }

        return [
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user->toArray()
        ];
    }

    /**
     * Get a user by ID
     *
     * @param string $id The ID of the user to get
     * @return array Result of the operation
     */
    public function getUserById(string $id): array
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        return [
            'success' => true,
            'data' => $user->toArray()
        ];
    }
}
<?php

namespace NTierArchitecture\Data;

use NTierArchitecture\Database\DatabaseConnection;
use NTierArchitecture\Model\User;

/**
 * User Repository
 * 
 * This class is part of the Data tier and handles data access for User entities.
 * It uses the Database connection to interact with the database.
 */
class UserRepository
{
    private DatabaseConnection $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance();
    }

    /**
     * Find a user by ID
     * 
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        $userData = $this->db->find('users', $id);
        if ($userData === null) {
            return null;
        }

        return $this->mapToUser($userData);
    }

    /**
     * Find a user by email
     * 
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        $userData = $this->db->findBy('users', 'email', $email);
        if ($userData === null) {
            return null;
        }

        return $this->mapToUser($userData);
    }

    /**
     * Save a user to the database
     * 
     * @param User $user
     * @return User
     */
    public function save(User $user): User
    {
        $userData = [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s')
        ];

        if ($user->getId() === null) {
            // Insert new user
            $id = $this->db->insert('users', $userData);
            $user->setId($id);
        } else {
            // Update existing user
            $this->db->update('users', $user->getId(), $userData);
        }

        return $user;
    }

    /**
     * Delete a user from the database
     * 
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        if ($user->getId() === null) {
            return false;
        }

        return $this->db->delete('users', $user->getId());
    }

    /**
     * Get all users from the database
     * 
     * @return User[]
     */
    public function findAll(): array
    {
        $usersData = $this->db->findAll('users');
        $users = [];

        foreach ($usersData as $userData) {
            $users[] = $this->mapToUser($userData);
        }

        return $users;
    }

    /**
     * Map database data to a User object
     * 
     * @param array $userData
     * @return User
     */
    private function mapToUser(array $userData): User
    {
        $user = new User(
            $userData['name'],
            $userData['email'],
            'dummy_password' // We don't need to set the real password as it's already hashed in the database
        );

        // Set the ID
        $user->setId($userData['id']);

        // Override the hashed password from the database
        $reflectionClass = new \ReflectionClass(User::class);
        $passwordProperty = $reflectionClass->getProperty('password');
        $passwordProperty->setAccessible(true);
        $passwordProperty->setValue($user, $userData['password']);

        // Override the created_at timestamp
        $createdAtProperty = $reflectionClass->getProperty('createdAt');
        $createdAtProperty->setAccessible(true);
        $createdAtProperty->setValue($user, new \DateTime($userData['created_at']));

        return $user;
    }
}
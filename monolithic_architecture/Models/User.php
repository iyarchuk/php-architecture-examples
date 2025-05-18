<?php

namespace MonolithicArchitecture\Models;

/**
 * User model represents user data and database interactions
 */
class User
{
    /**
     * Get a user by ID
     * 
     * @param int $userId User ID
     * @return array|null User data or null if not found
     */
    public function getById(int $userId): ?array
    {
        // In a real application, we would fetch the user from the database
        // For this example, we'll simulate a user
        if ($userId === 1) {
            return [
                'id' => 1,
                'email' => 'john.doe@example.com',
                'name' => 'John Doe',
                'role' => 'user',
                'created_at' => '2023-01-01 00:00:00'
            ];
        }

        return null;
    }

    /**
     * Get a user by email
     * 
     * @param string $email User email
     * @return array|null User data or null if not found
     */
    public function getByEmail(string $email): ?array
    {
        // In a real application, we would fetch the user from the database
        // For this example, we'll simulate a user
        if ($email === 'john.doe@example.com') {
            return [
                'id' => 1,
                'email' => 'john.doe@example.com',
                'name' => 'John Doe',
                'role' => 'user',
                'created_at' => '2023-01-01 00:00:00'
            ];
        }

        return null;
    }

    /**
     * Create a new user
     * 
     * @param array $data User data
     * @return array Created user data
     */
    public function create(array $data): array
    {
        // In a real application, we would save the user to the database
        // For this example, we'll simulate creating a user
        return [
            'id' => 2, // Simulate a new ID
            'email' => $data['email'],
            'name' => $data['name'],
            'role' => $data['role'] ?? 'user',
            'created_at' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Update a user
     * 
     * @param int $userId User ID
     * @param array $data User data to update
     * @return array Updated user data
     */
    public function update(int $userId, array $data): array
    {
        // In a real application, we would update the user in the database
        // For this example, we'll simulate an update
        return [
            'id' => $userId,
            'email' => $data['email'],
            'name' => $data['name'],
            'role' => $data['role'],
            'created_at' => $data['created_at'],
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Delete a user
     * 
     * @param int $userId User ID
     * @return bool True if deleted, false otherwise
     */
    public function delete(int $userId): bool
    {
        // In a real application, we would delete the user from the database
        // For this example, we'll simulate a deletion
        return true;
    }

    /**
     * Get all users
     * 
     * @return array List of users
     */
    public function getAll(): array
    {
        // In a real application, we would fetch all users from the database
        // For this example, we'll simulate some users
        return [
            [
                'id' => 1,
                'email' => 'john.doe@example.com',
                'name' => 'John Doe',
                'role' => 'user',
                'created_at' => '2023-01-01 00:00:00'
            ],
            [
                'id' => 2,
                'email' => 'jane.smith@example.com',
                'name' => 'Jane Smith',
                'role' => 'admin',
                'created_at' => '2023-01-02 00:00:00'
            ]
        ];
    }

    /**
     * Authenticate a user
     * 
     * @param string $email User email
     * @param string $password User password
     * @return array|null User data or null if authentication fails
     */
    public function authenticate(string $email, string $password): ?array
    {
        // In a real application, we would check the credentials against the database
        // For this example, we'll simulate a successful authentication
        if ($email === 'john.doe@example.com' && $password === 'password123') {
            return [
                'id' => 1,
                'email' => 'john.doe@example.com',
                'name' => 'John Doe',
                'role' => 'user',
                'created_at' => '2023-01-01 00:00:00'
            ];
        }

        return null;
    }
}
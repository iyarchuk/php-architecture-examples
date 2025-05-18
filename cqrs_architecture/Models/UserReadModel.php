<?php

namespace CQRSArchitecture\Models;

/**
 * UserReadModel
 * 
 * This class represents the read model for a user.
 * It is optimized for querying and does not contain business logic.
 */
class UserReadModel
{
    /**
     * @var array Simulated database of users
     */
    private static array $users = [];
    
    /**
     * @var int Auto-increment ID
     */
    private static int $nextId = 1;
    
    /**
     * Add a user to the read model
     * 
     * @param int $id The ID of the user
     * @param string $name The name of the user
     * @param string $email The email of the user
     * @param string $createdAt The date the user was created
     * @return void
     */
    public static function addUser(int $id, string $name, string $email, string $createdAt): void
    {
        self::$users[$id] = [
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'created_at' => $createdAt
        ];
    }
    
    /**
     * Update a user in the read model
     * 
     * @param int $id The ID of the user
     * @param array $changes The changes to apply
     * @return void
     */
    public static function updateUser(int $id, array $changes): void
    {
        if (!isset(self::$users[$id])) {
            return;
        }
        
        foreach ($changes as $key => $value) {
            if ($key === 'password') {
                continue; // Don't store passwords in the read model
            }
            
            self::$users[$id][$key] = $value;
        }
    }
    
    /**
     * Delete a user from the read model
     * 
     * @param int $id The ID of the user
     * @return void
     */
    public static function deleteUser(int $id): void
    {
        if (isset(self::$users[$id])) {
            unset(self::$users[$id]);
        }
    }
    
    /**
     * Get a user by ID
     * 
     * @param int $id The ID of the user
     * @return array|null
     */
    public static function getUserById(int $id): ?array
    {
        return self::$users[$id] ?? null;
    }
    
    /**
     * Get all users
     * 
     * @param array $filters Optional filters
     * @return array
     */
    public static function getAllUsers(array $filters = []): array
    {
        if (empty($filters)) {
            return self::$users;
        }
        
        return array_filter(self::$users, function ($user) use ($filters) {
            foreach ($filters as $key => $value) {
                if (!isset($user[$key]) || $user[$key] !== $value) {
                    return false;
                }
            }
            return true;
        });
    }
    
    /**
     * Generate a new user ID
     * 
     * @return int
     */
    public static function generateId(): int
    {
        return self::$nextId++;
    }
    
    /**
     * Initialize the read model with some sample data
     * 
     * @return void
     */
    public static function initialize(): void
    {
        self::$users = [];
        self::$nextId = 1;
        
        self::addUser(
            self::generateId(),
            'John Doe',
            'john.doe@example.com',
            date('Y-m-d H:i:s')
        );
        
        self::addUser(
            self::generateId(),
            'Jane Smith',
            'jane.smith@example.com',
            date('Y-m-d H:i:s')
        );
    }
}
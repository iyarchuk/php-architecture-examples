<?php

namespace MVVMArchitecture\Models;

/**
 * User Model
 * 
 * This class represents the Model component in MVVM.
 * It handles data and business logic related to users.
 */
class UserModel
{
    /**
     * @var array Simulated database of users
     */
    private array $users = [];

    /**
     * @var int Auto-increment ID
     */
    private int $nextId = 1;

    /**
     * @var array List of observers
     */
    private array $observers = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Initialize with some sample data
        $this->addUser('John Doe', 'john.doe@example.com', 'password123');
        $this->addUser('Jane Smith', 'jane.smith@example.com', 'password456');
    }

    /**
     * Add a user
     * 
     * @param string $name
     * @param string $email
     * @param string $password
     * @return array The created user
     * @throws \InvalidArgumentException
     */
    public function addUser(string $name, string $email, string $password): array
    {
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        // Validate password
        if (strlen($password) < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters long');
        }

        // Check if email already exists
        foreach ($this->users as $user) {
            if ($user['email'] === $email) {
                throw new \InvalidArgumentException('Email already exists');
            }
        }

        // Create user
        $user = [
            'id' => $this->nextId++,
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Add to users array
        $this->users[$user['id']] = $user;

        // Notify observers
        $this->notifyObservers();

        return $user;
    }

    /**
     * Get a user by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function getUser(int $id): ?array
    {
        return $this->users[$id] ?? null;
    }

    /**
     * Get all users
     * 
     * @return array
     */
    public function getAllUsers(): array
    {
        return $this->users;
    }

    /**
     * Update a user
     * 
     * @param int $id
     * @param array $data
     * @return array|null
     * @throws \InvalidArgumentException
     */
    public function updateUser(int $id, array $data): ?array
    {
        if (!isset($this->users[$id])) {
            return null;
        }

        $user = $this->users[$id];

        // Update name if provided
        if (isset($data['name'])) {
            $user['name'] = $data['name'];
        }

        // Update email if provided
        if (isset($data['email'])) {
            // Validate email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException('Invalid email format');
            }

            // Check if email already exists
            foreach ($this->users as $existingUser) {
                if ($existingUser['email'] === $data['email'] && $existingUser['id'] !== $id) {
                    throw new \InvalidArgumentException('Email already exists');
                }
            }

            $user['email'] = $data['email'];
        }

        // Update password if provided
        if (isset($data['password'])) {
            // Validate password
            if (strlen($data['password']) < 8) {
                throw new \InvalidArgumentException('Password must be at least 8 characters long');
            }

            $user['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        // Update user in array
        $this->users[$id] = $user;

        // Notify observers
        $this->notifyObservers();

        return $user;
    }

    /**
     * Delete a user
     * 
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): bool
    {
        if (!isset($this->users[$id])) {
            return false;
        }

        unset($this->users[$id]);

        // Notify observers
        $this->notifyObservers();

        return true;
    }

    /**
     * Authenticate a user
     * 
     * @param string $email
     * @param string $password
     * @return array|null
     */
    public function authenticateUser(string $email, string $password): ?array
    {
        foreach ($this->users as $user) {
            if ($user['email'] === $email && password_verify($password, $user['password'])) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Add an observer
     * 
     * @param object $observer
     * @return void
     */
    public function addObserver(object $observer): void
    {
        $this->observers[] = $observer;
    }

    /**
     * Notify all observers
     * 
     * @return void
     */
    private function notifyObservers(): void
    {
        foreach ($this->observers as $observer) {
            if (method_exists($observer, 'update')) {
                $observer->update($this);
            }
        }
    }
}
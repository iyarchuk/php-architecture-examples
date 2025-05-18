<?php

namespace ClientServerArchitecture\Client;

use ClientServerArchitecture\Shared\Response;

/**
 * UserInterface
 * 
 * This class provides a user interface for interacting with the system.
 * It displays information to the user and captures user input.
 */
class UserInterface
{
    /**
     * @var Client The client instance
     */
    private Client $client;
    
    /**
     * Constructor
     * 
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
    
    /**
     * Display a welcome message
     * 
     * @return void
     */
    public function displayWelcome(): void
    {
        echo "=== User Management System ===\n";
        echo "Welcome to the User Management System!\n\n";
    }
    
    /**
     * Display a list of users
     * 
     * @return void
     */
    public function displayUsers(): void
    {
        echo "\n=== User List ===\n";
        
        $response = $this->client->getAllUsers();
        
        if (!$response->isSuccess()) {
            echo "Error: {$response->getMessage()}\n";
            return;
        }
        
        $users = $response->getData();
        
        if (empty($users)) {
            echo "No users found.\n";
            return;
        }
        
        echo str_pad("ID", 5) . str_pad("Name", 20) . str_pad("Email", 30) . "Created At\n";
        echo str_repeat("-", 75) . "\n";
        
        foreach ($users as $user) {
            echo str_pad($user['id'], 5) . 
                 str_pad($user['name'], 20) . 
                 str_pad($user['email'], 30) . 
                 $user['created_at'] . "\n";
        }
        
        echo "\n";
    }
    
    /**
     * Display a single user
     * 
     * @param int $id
     * @return void
     */
    public function displayUser(int $id): void
    {
        echo "\n=== User Details ===\n";
        
        $response = $this->client->getUser($id);
        
        if (!$response->isSuccess()) {
            echo "Error: {$response->getMessage()}\n";
            return;
        }
        
        $user = $response->getData();
        
        echo "ID: {$user['id']}\n";
        echo "Name: {$user['name']}\n";
        echo "Email: {$user['email']}\n";
        echo "Created At: {$user['created_at']}\n\n";
    }
    
    /**
     * Create a user
     * 
     * @param string $name
     * @param string $email
     * @param string $password
     * @return void
     */
    public function createUser(string $name, string $email, string $password): void
    {
        echo "\n=== Create User ===\n";
        
        // Validate input
        if (empty($name) || empty($email) || empty($password)) {
            echo "Error: All fields are required.\n";
            return;
        }
        
        $response = $this->client->createUser($name, $email, $password);
        
        if ($response->isSuccess()) {
            $user = $response->getData();
            echo "User created successfully with ID: {$user['id']}\n";
        } else {
            echo "Error: {$response->getMessage()}\n";
        }
    }
    
    /**
     * Update a user
     * 
     * @param int $id
     * @param array $data
     * @return void
     */
    public function updateUser(int $id, array $data): void
    {
        echo "\n=== Update User ===\n";
        
        $response = $this->client->updateUser($id, $data);
        
        if ($response->isSuccess()) {
            echo "User updated successfully.\n";
        } else {
            echo "Error: {$response->getMessage()}\n";
        }
    }
    
    /**
     * Delete a user
     * 
     * @param int $id
     * @return void
     */
    public function deleteUser(int $id): void
    {
        echo "\n=== Delete User ===\n";
        
        $response = $this->client->deleteUser($id);
        
        if ($response->isSuccess()) {
            echo "User deleted successfully.\n";
        } else {
            echo "Error: {$response->getMessage()}\n";
        }
    }
    
    /**
     * Authenticate a user
     * 
     * @param string $email
     * @param string $password
     * @return void
     */
    public function authenticateUser(string $email, string $password): void
    {
        echo "\n=== Authenticate User ===\n";
        
        $response = $this->client->authenticateUser($email, $password);
        
        if ($response->isSuccess()) {
            $user = $response->getData();
            echo "Authentication successful. Welcome, {$user['name']}!\n";
        } else {
            echo "Error: {$response->getMessage()}\n";
        }
    }
    
    /**
     * Display a response
     * 
     * @param Response $response
     * @return void
     */
    public function displayResponse(Response $response): void
    {
        if ($response->isSuccess()) {
            echo "Success: {$response->getMessage()}\n";
            
            $data = $response->getData();
            if ($data) {
                echo "Data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
            }
        } else {
            echo "Error: {$response->getMessage()}\n";
        }
    }
}
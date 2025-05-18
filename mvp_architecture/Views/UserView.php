<?php

namespace MVPArchitecture\Views;

/**
 * User View
 * 
 * This class implements the IUserView interface and represents the View component in MVP.
 * It is responsible for displaying data to the user and capturing user input.
 */
class UserView implements IUserView
{
    /**
     * @var int|null The user ID for the current operation
     */
    private ?int $userId = null;
    
    /**
     * @var array The user data for the current operation
     */
    private array $userData = [];
    
    /**
     * Display a list of users
     * 
     * @param array $users
     * @return void
     */
    public function displayUsers(array $users): void
    {
        echo "\n=== USER LIST ===\n";
        
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
     * @param array $user
     * @return void
     */
    public function displayUser(array $user): void
    {
        echo "\n=== USER DETAILS ===\n";
        echo "ID: {$user['id']}\n";
        echo "Name: {$user['name']}\n";
        echo "Email: {$user['email']}\n";
        echo "Created At: {$user['created_at']}\n\n";
    }
    
    /**
     * Display the user creation form
     * 
     * @return void
     */
    public function displayCreateForm(): void
    {
        echo "\n=== CREATE USER ===\n";
        echo "This is a simulated form for creating a new user.\n";
        echo "In a real application, this would be an HTML form.\n";
        echo "\nFields:\n";
        echo "- Name (required)\n";
        echo "- Email (required, must be valid email format)\n";
        echo "- Password (required, minimum 8 characters)\n\n";
    }
    
    /**
     * Display the user edit form
     * 
     * @param array $user
     * @return void
     */
    public function displayEditForm(array $user): void
    {
        echo "\n=== EDIT USER ===\n";
        echo "This is a simulated form for editing user with ID: {$user['id']}\n";
        echo "In a real application, this would be an HTML form with pre-filled values.\n";
        echo "\nCurrent values:\n";
        echo "- Name: {$user['name']}\n";
        echo "- Email: {$user['email']}\n";
        echo "- Password: [hidden]\n";
        echo "\nFields that can be updated:\n";
        echo "- Name (optional)\n";
        echo "- Email (optional, must be valid email format)\n";
        echo "- Password (optional, minimum 8 characters)\n\n";
        
        // Set the user ID for the current operation
        $this->userId = $user['id'];
    }
    
    /**
     * Display a success message
     * 
     * @param string $message
     * @return void
     */
    public function displaySuccess(string $message): void
    {
        echo "\n=== SUCCESS ===\n";
        echo "$message\n\n";
    }
    
    /**
     * Display an error message
     * 
     * @param string $message
     * @return void
     */
    public function displayError(string $message): void
    {
        echo "\n=== ERROR ===\n";
        echo "$message\n\n";
    }
    
    /**
     * Get the user ID from the view
     * 
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }
    
    /**
     * Set the user ID for the current operation
     * 
     * @param int $id
     * @return void
     */
    public function setUserId(int $id): void
    {
        $this->userId = $id;
    }
    
    /**
     * Get the user data from the view
     * 
     * @return array
     */
    public function getUserData(): array
    {
        return $this->userData;
    }
    
    /**
     * Set the user data for the current operation
     * 
     * @param array $data
     * @return void
     */
    public function setUserData(array $data): void
    {
        $this->userData = $data;
    }
    
    /**
     * Simulate user input for creating a user
     * 
     * @param string $name
     * @param string $email
     * @param string $password
     * @return void
     */
    public function simulateCreateUserInput(string $name, string $email, string $password): void
    {
        $this->userData = [
            'name' => $name,
            'email' => $email,
            'password' => $password
        ];
    }
    
    /**
     * Simulate user input for updating a user
     * 
     * @param int $id
     * @param array $data
     * @return void
     */
    public function simulateUpdateUserInput(int $id, array $data): void
    {
        $this->userId = $id;
        $this->userData = $data;
    }
    
    /**
     * Simulate user input for deleting a user
     * 
     * @param int $id
     * @return void
     */
    public function simulateDeleteUserInput(int $id): void
    {
        $this->userId = $id;
    }
}
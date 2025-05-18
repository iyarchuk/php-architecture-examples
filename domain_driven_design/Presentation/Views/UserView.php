<?php

namespace DomainDrivenDesign\Presentation\Views;

use DomainDrivenDesign\Application\DTOs\UserDTO;

/**
 * UserView
 * 
 * This class represents a view for displaying user information.
 * In DDD, views are part of the presentation layer and are responsible for displaying data to the user.
 */
class UserView
{
    /**
     * Display a user
     * 
     * @param UserDTO $userDTO
     * @return void
     */
    public function displayUser(UserDTO $userDTO): void
    {
        echo "\n=== USER DETAILS ===\n";
        echo "ID: {$userDTO->getId()}\n";
        echo "Name: {$userDTO->getName()}\n";
        echo "Email: {$userDTO->getEmail()}\n";
        echo "Created At: {$userDTO->getCreatedAt()}\n";
        
        // Display roles
        echo "\nRoles: ";
        if (empty($userDTO->getRoles())) {
            echo "None";
        } else {
            echo implode(', ', $userDTO->getRoles());
        }
        echo "\n";
        
        // Display profile if not empty
        if (!empty($userDTO->getProfile())) {
            echo "\nProfile:\n";
            foreach ($userDTO->getProfile() as $key => $value) {
                echo "- $key: $value\n";
            }
        }
        
        // Display preferences if not empty
        if (!empty($userDTO->getPreferences())) {
            echo "\nPreferences:\n";
            foreach ($userDTO->getPreferences() as $key => $value) {
                echo "- $key: $value\n";
            }
        }
        
        echo "\n";
    }
    
    /**
     * Display a list of users
     * 
     * @param array $userDTOs
     * @return void
     */
    public function displayUserList(array $userDTOs): void
    {
        echo "\n=== USER LIST ===\n";
        
        if (empty($userDTOs)) {
            echo "No users found.\n\n";
            return;
        }
        
        echo str_pad("ID", 38) . str_pad("Name", 20) . "Email\n";
        echo str_repeat("-", 80) . "\n";
        
        foreach ($userDTOs as $userDTO) {
            echo str_pad($userDTO->getId(), 38) . 
                 str_pad($userDTO->getName(), 20) . 
                 $userDTO->getEmail() . "\n";
        }
        
        echo "\n";
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
     * Display a response from the controller
     * 
     * @param array $response
     * @return void
     */
    public function displayResponse(array $response): void
    {
        if ($response['success']) {
            $this->displaySuccess($response['message']);
            
            // If there's data and it's a user, display it
            if (isset($response['data']) && is_array($response['data'])) {
                if (isset($response['data']['id'])) {
                    // Single user
                    $this->displayUserFromArray($response['data']);
                } elseif (is_array($response['data']) && !empty($response['data']) && isset($response['data'][0]['id'])) {
                    // List of users
                    $this->displayUserListFromArray($response['data']);
                }
            }
        } else {
            $this->displayError($response['message']);
        }
    }
    
    /**
     * Display a user from an array
     * 
     * @param array $userData
     * @return void
     */
    private function displayUserFromArray(array $userData): void
    {
        echo "\n=== USER DETAILS ===\n";
        echo "ID: {$userData['id']}\n";
        echo "Name: {$userData['name']}\n";
        echo "Email: {$userData['email']}\n";
        echo "Created At: {$userData['created_at']}\n";
        
        // Display roles
        echo "\nRoles: ";
        if (empty($userData['roles'])) {
            echo "None";
        } else {
            echo implode(', ', $userData['roles']);
        }
        echo "\n";
        
        // Display profile if not empty
        if (!empty($userData['profile'])) {
            echo "\nProfile:\n";
            foreach ($userData['profile'] as $key => $value) {
                echo "- $key: $value\n";
            }
        }
        
        // Display preferences if not empty
        if (!empty($userData['preferences'])) {
            echo "\nPreferences:\n";
            foreach ($userData['preferences'] as $key => $value) {
                echo "- $key: $value\n";
            }
        }
        
        echo "\n";
    }
    
    /**
     * Display a list of users from an array
     * 
     * @param array $usersData
     * @return void
     */
    private function displayUserListFromArray(array $usersData): void
    {
        echo "\n=== USER LIST ===\n";
        
        if (empty($usersData)) {
            echo "No users found.\n\n";
            return;
        }
        
        echo str_pad("ID", 38) . str_pad("Name", 20) . "Email\n";
        echo str_repeat("-", 80) . "\n";
        
        foreach ($usersData as $userData) {
            echo str_pad($userData['id'], 38) . 
                 str_pad($userData['name'], 20) . 
                 $userData['email'] . "\n";
        }
        
        echo "\n";
    }
}
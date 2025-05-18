<?php

namespace MVCArchitecture\Views;

use MVCArchitecture\Models\UserModel;

/**
 * User Form View
 * 
 * This class represents a View component in MVC.
 * It displays forms for creating and editing users.
 */
class UserFormView
{
    /**
     * Update method called by the model when data changes
     * 
     * @param UserModel $model
     * @return void
     */
    public function update(UserModel $model): void
    {
        // This view doesn't need to update automatically when the model changes
        // It will be explicitly rendered by the controller
    }
    
    /**
     * Render the user creation form
     * 
     * @return void
     */
    public function renderCreateForm(): void
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
     * Render the user edit form
     * 
     * @param array $user
     * @return void
     */
    public function renderEditForm(array $user): void
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
    }
    
    /**
     * Render a confirmation message
     * 
     * @param string $message
     * @return void
     */
    public function renderConfirmation(string $message): void
    {
        echo "\n=== CONFIRMATION ===\n";
        echo "$message\n\n";
    }
    
    /**
     * Render an error message
     * 
     * @param string $message
     * @return void
     */
    public function renderError(string $message): void
    {
        echo "\n=== ERROR ===\n";
        echo "$message\n\n";
    }
}
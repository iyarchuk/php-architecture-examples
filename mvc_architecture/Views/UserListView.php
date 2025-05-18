<?php

namespace MVCArchitecture\Views;

use MVCArchitecture\Models\UserModel;

/**
 * User List View
 * 
 * This class represents a View component in MVC.
 * It displays a list of users.
 */
class UserListView
{
    /**
     * Update method called by the model when data changes
     * 
     * @param UserModel $model
     * @return void
     */
    public function update(UserModel $model): void
    {
        $this->render($model->getAllUsers());
    }
    
    /**
     * Render the user list
     * 
     * @param array $users
     * @return void
     */
    public function render(array $users): void
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
}
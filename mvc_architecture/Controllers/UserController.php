<?php

namespace MVCArchitecture\Controllers;

use MVCArchitecture\Models\UserModel;
use MVCArchitecture\Views\UserListView;
use MVCArchitecture\Views\UserFormView;

/**
 * User Controller
 * 
 * This class represents the Controller component in MVC.
 * It handles user requests and updates the model and view accordingly.
 */
class UserController
{
    /**
     * @var UserModel The user model
     */
    private UserModel $model;
    
    /**
     * @var UserListView The user list view
     */
    private UserListView $listView;
    
    /**
     * @var UserFormView The user form view
     */
    private UserFormView $formView;
    
    /**
     * Constructor
     * 
     * @param UserModel $model
     * @param UserListView $listView
     * @param UserFormView $formView
     */
    public function __construct(UserModel $model, UserListView $listView, UserFormView $formView)
    {
        $this->model = $model;
        $this->listView = $listView;
        $this->formView = $formView;
        
        // Register views as observers of the model
        $this->model->addObserver($this->listView);
        $this->model->addObserver($this->formView);
    }
    
    /**
     * Show the list of users
     * 
     * @return void
     */
    public function showUsers(): void
    {
        $users = $this->model->getAllUsers();
        $this->listView->render($users);
    }
    
    /**
     * Show the user creation form
     * 
     * @return void
     */
    public function showCreateForm(): void
    {
        $this->formView->renderCreateForm();
    }
    
    /**
     * Show the user edit form
     * 
     * @param int $id
     * @return void
     */
    public function showEditForm(int $id): void
    {
        $user = $this->model->getUser($id);
        if ($user) {
            $this->formView->renderEditForm($user);
        } else {
            echo "User not found.\n";
        }
    }
    
    /**
     * Create a new user
     * 
     * @param string $name
     * @param string $email
     * @param string $password
     * @return void
     */
    public function createUser(string $name, string $email, string $password): void
    {
        try {
            $user = $this->model->addUser($name, $email, $password);
            echo "User created successfully with ID: {$user['id']}\n";
            $this->showUsers();
        } catch (\InvalidArgumentException $e) {
            echo "Error: {$e->getMessage()}\n";
            $this->showCreateForm();
        }
    }
    
    /**
     * Update an existing user
     * 
     * @param int $id
     * @param array $data
     * @return void
     */
    public function updateUser(int $id, array $data): void
    {
        try {
            $user = $this->model->updateUser($id, $data);
            if ($user) {
                echo "User updated successfully.\n";
                $this->showUsers();
            } else {
                echo "User not found.\n";
            }
        } catch (\InvalidArgumentException $e) {
            echo "Error: {$e->getMessage()}\n";
            $this->showEditForm($id);
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
        $result = $this->model->deleteUser($id);
        if ($result) {
            echo "User deleted successfully.\n";
        } else {
            echo "User not found.\n";
        }
        $this->showUsers();
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
        $user = $this->model->authenticateUser($email, $password);
        if ($user) {
            echo "Authentication successful. Welcome, {$user['name']}!\n";
        } else {
            echo "Authentication failed. Invalid email or password.\n";
        }
    }
}
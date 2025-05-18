<?php

namespace MVPArchitecture\Presenters;

use MVPArchitecture\Models\UserModel;
use MVPArchitecture\Views\IUserView;

/**
 * User Presenter
 * 
 * This class represents the Presenter component in MVP.
 * It acts as a mediator between the Model and View.
 */
class UserPresenter
{
    /**
     * @var UserModel The user model
     */
    private UserModel $model;
    
    /**
     * @var IUserView The user view
     */
    private IUserView $view;
    
    /**
     * Constructor
     * 
     * @param UserModel $model
     * @param IUserView $view
     */
    public function __construct(UserModel $model, IUserView $view)
    {
        $this->model = $model;
        $this->view = $view;
    }
    
    /**
     * Load and display all users
     * 
     * @return void
     */
    public function loadUsers(): void
    {
        $users = $this->model->getAllUsers();
        $this->view->displayUsers($users);
    }
    
    /**
     * Load and display a single user
     * 
     * @param int $id
     * @return void
     */
    public function loadUser(int $id): void
    {
        $user = $this->model->getUser($id);
        if ($user) {
            $this->view->displayUser($user);
        } else {
            $this->view->displayError("User with ID $id not found.");
        }
    }
    
    /**
     * Show the user creation form
     * 
     * @return void
     */
    public function showCreateForm(): void
    {
        $this->view->displayCreateForm();
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
            $this->view->displayEditForm($user);
        } else {
            $this->view->displayError("User with ID $id not found.");
        }
    }
    
    /**
     * Create a new user
     * 
     * @return void
     */
    public function createUser(): void
    {
        $userData = $this->view->getUserData();
        
        try {
            $user = $this->model->addUser(
                $userData['name'],
                $userData['email'],
                $userData['password']
            );
            
            $this->view->displaySuccess("User created successfully with ID: {$user['id']}");
            $this->loadUsers();
        } catch (\InvalidArgumentException $e) {
            $this->view->displayError("Error: {$e->getMessage()}");
            $this->view->displayCreateForm();
        }
    }
    
    /**
     * Update an existing user
     * 
     * @return void
     */
    public function updateUser(): void
    {
        $id = $this->view->getUserId();
        $userData = $this->view->getUserData();
        
        if (!$id) {
            $this->view->displayError("No user ID provided for update.");
            return;
        }
        
        try {
            $user = $this->model->updateUser($id, $userData);
            
            if ($user) {
                $this->view->displaySuccess("User updated successfully.");
                $this->loadUsers();
            } else {
                $this->view->displayError("User with ID $id not found.");
            }
        } catch (\InvalidArgumentException $e) {
            $this->view->displayError("Error: {$e->getMessage()}");
            $this->showEditForm($id);
        }
    }
    
    /**
     * Delete a user
     * 
     * @return void
     */
    public function deleteUser(): void
    {
        $id = $this->view->getUserId();
        
        if (!$id) {
            $this->view->displayError("No user ID provided for deletion.");
            return;
        }
        
        $result = $this->model->deleteUser($id);
        
        if ($result) {
            $this->view->displaySuccess("User deleted successfully.");
        } else {
            $this->view->displayError("User with ID $id not found.");
        }
        
        $this->loadUsers();
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
            $this->view->displaySuccess("Authentication successful. Welcome, {$user['name']}!");
        } else {
            $this->view->displayError("Authentication failed. Invalid email or password.");
        }
    }
}
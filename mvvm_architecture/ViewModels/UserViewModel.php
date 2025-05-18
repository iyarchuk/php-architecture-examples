<?php

namespace MVVMArchitecture\ViewModels;

use MVVMArchitecture\Models\UserModel;

/**
 * User ViewModel
 * 
 * This class represents the ViewModel component in MVVM.
 * It acts as a mediator between the Model and View.
 */
class UserViewModel
{
    /**
     * @var UserModel The user model
     */
    private UserModel $model;
    
    /**
     * @var array List of observers (views)
     */
    private array $observers = [];
    
    /**
     * @var array Current user list
     */
    private array $userList = [];
    
    /**
     * @var array|null Current selected user
     */
    private ?array $selectedUser = null;
    
    /**
     * @var string|null Success message
     */
    private ?string $successMessage = null;
    
    /**
     * @var string|null Error message
     */
    private ?string $errorMessage = null;
    
    /**
     * @var bool Whether to show the create form
     */
    private bool $showCreateForm = false;
    
    /**
     * @var bool Whether to show the edit form
     */
    private bool $showEditForm = false;
    
    /**
     * Constructor
     * 
     * @param UserModel $model
     */
    public function __construct(UserModel $model)
    {
        $this->model = $model;
        $this->model->addObserver($this);
        $this->refreshUserList();
    }
    
    /**
     * Update method called by the model when data changes
     * 
     * @param UserModel $model
     * @return void
     */
    public function update(UserModel $model): void
    {
        $this->refreshUserList();
        $this->notifyObservers();
    }
    
    /**
     * Refresh the user list from the model
     * 
     * @return void
     */
    private function refreshUserList(): void
    {
        $this->userList = $this->model->getAllUsers();
    }
    
    /**
     * Get the user list
     * 
     * @return array
     */
    public function getUserList(): array
    {
        return $this->userList;
    }
    
    /**
     * Get the selected user
     * 
     * @return array|null
     */
    public function getSelectedUser(): ?array
    {
        return $this->selectedUser;
    }
    
    /**
     * Get the success message
     * 
     * @return string|null
     */
    public function getSuccessMessage(): ?string
    {
        return $this->successMessage;
    }
    
    /**
     * Get the error message
     * 
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }
    
    /**
     * Check if the create form should be shown
     * 
     * @return bool
     */
    public function shouldShowCreateForm(): bool
    {
        return $this->showCreateForm;
    }
    
    /**
     * Check if the edit form should be shown
     * 
     * @return bool
     */
    public function shouldShowEditForm(): bool
    {
        return $this->showEditForm;
    }
    
    /**
     * Select a user by ID
     * 
     * @param int $id
     * @return void
     */
    public function selectUser(int $id): void
    {
        $this->selectedUser = $this->model->getUser($id);
        
        if (!$this->selectedUser) {
            $this->errorMessage = "User with ID $id not found.";
        } else {
            $this->errorMessage = null;
        }
        
        $this->notifyObservers();
    }
    
    /**
     * Show the create form
     * 
     * @return void
     */
    public function showCreateForm(): void
    {
        $this->showCreateForm = true;
        $this->showEditForm = false;
        $this->successMessage = null;
        $this->errorMessage = null;
        $this->notifyObservers();
    }
    
    /**
     * Show the edit form for a user
     * 
     * @param int $id
     * @return void
     */
    public function showEditForm(int $id): void
    {
        $this->selectUser($id);
        
        if ($this->selectedUser) {
            $this->showEditForm = true;
            $this->showCreateForm = false;
            $this->successMessage = null;
            $this->errorMessage = null;
        }
        
        $this->notifyObservers();
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
            $this->successMessage = "User created successfully with ID: {$user['id']}";
            $this->errorMessage = null;
            $this->showCreateForm = false;
        } catch (\InvalidArgumentException $e) {
            $this->errorMessage = "Error: {$e->getMessage()}";
            $this->successMessage = null;
        }
        
        $this->notifyObservers();
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
                $this->successMessage = "User updated successfully.";
                $this->errorMessage = null;
                $this->showEditForm = false;
                $this->selectedUser = $user;
            } else {
                $this->errorMessage = "User with ID $id not found.";
                $this->successMessage = null;
            }
        } catch (\InvalidArgumentException $e) {
            $this->errorMessage = "Error: {$e->getMessage()}";
            $this->successMessage = null;
        }
        
        $this->notifyObservers();
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
            $this->successMessage = "User deleted successfully.";
            $this->errorMessage = null;
            
            if ($this->selectedUser && $this->selectedUser['id'] === $id) {
                $this->selectedUser = null;
                $this->showEditForm = false;
            }
        } else {
            $this->errorMessage = "User with ID $id not found.";
            $this->successMessage = null;
        }
        
        $this->notifyObservers();
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
            $this->successMessage = "Authentication successful. Welcome, {$user['name']}!";
            $this->errorMessage = null;
            $this->selectedUser = $user;
        } else {
            $this->errorMessage = "Authentication failed. Invalid email or password.";
            $this->successMessage = null;
        }
        
        $this->notifyObservers();
    }
    
    /**
     * Add an observer (view)
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
    
    /**
     * Clear messages
     * 
     * @return void
     */
    public function clearMessages(): void
    {
        $this->successMessage = null;
        $this->errorMessage = null;
        $this->notifyObservers();
    }
}
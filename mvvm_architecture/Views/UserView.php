<?php

namespace MVVMArchitecture\Views;

use MVVMArchitecture\ViewModels\UserViewModel;

/**
 * User View
 * 
 * This class represents the View component in MVVM.
 * It is responsible for displaying data to the user and capturing user input.
 */
class UserView
{
    /**
     * @var UserViewModel The user view model
     */
    private UserViewModel $viewModel;
    
    /**
     * @var array User input data for the current operation
     */
    private array $inputData = [];
    
    /**
     * Constructor
     * 
     * @param UserViewModel $viewModel
     */
    public function __construct(UserViewModel $viewModel)
    {
        $this->viewModel = $viewModel;
        $this->viewModel->addObserver($this);
    }
    
    /**
     * Update method called by the view model when data changes
     * 
     * @param UserViewModel $viewModel
     * @return void
     */
    public function update(UserViewModel $viewModel): void
    {
        $this->render();
    }
    
    /**
     * Render the view based on the current state of the view model
     * 
     * @return void
     */
    public function render(): void
    {
        // Display success message if any
        if ($this->viewModel->getSuccessMessage()) {
            $this->displaySuccess($this->viewModel->getSuccessMessage());
        }
        
        // Display error message if any
        if ($this->viewModel->getErrorMessage()) {
            $this->displayError($this->viewModel->getErrorMessage());
        }
        
        // Display create form if needed
        if ($this->viewModel->shouldShowCreateForm()) {
            $this->displayCreateForm();
            return;
        }
        
        // Display edit form if needed
        if ($this->viewModel->shouldShowEditForm()) {
            $user = $this->viewModel->getSelectedUser();
            if ($user) {
                $this->displayEditForm($user);
            }
            return;
        }
        
        // Display selected user if any
        $selectedUser = $this->viewModel->getSelectedUser();
        if ($selectedUser) {
            $this->displayUser($selectedUser);
        }
        
        // Display user list
        $this->displayUsers($this->viewModel->getUserList());
    }
    
    /**
     * Display a list of users
     * 
     * @param array $users
     * @return void
     */
    private function displayUsers(array $users): void
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
    private function displayUser(array $user): void
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
    private function displayCreateForm(): void
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
    private function displayEditForm(array $user): void
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
     * Display a success message
     * 
     * @param string $message
     * @return void
     */
    private function displaySuccess(string $message): void
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
    private function displayError(string $message): void
    {
        echo "\n=== ERROR ===\n";
        echo "$message\n\n";
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
        $this->viewModel->showCreateForm();
        $this->viewModel->createUser($name, $email, $password);
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
        $this->viewModel->showEditForm($id);
        $this->viewModel->updateUser($id, $data);
    }
    
    /**
     * Simulate user input for deleting a user
     * 
     * @param int $id
     * @return void
     */
    public function simulateDeleteUserInput(int $id): void
    {
        $this->viewModel->deleteUser($id);
    }
    
    /**
     * Simulate user input for selecting a user
     * 
     * @param int $id
     * @return void
     */
    public function simulateSelectUserInput(int $id): void
    {
        $this->viewModel->selectUser($id);
    }
    
    /**
     * Simulate user input for authenticating
     * 
     * @param string $email
     * @param string $password
     * @return void
     */
    public function simulateAuthenticateInput(string $email, string $password): void
    {
        $this->viewModel->authenticateUser($email, $password);
    }
}
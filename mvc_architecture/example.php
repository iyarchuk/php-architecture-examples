<?php

// Include autoloader (in a real application, you would use Composer's autoloader)
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $file = __DIR__ . '/' . str_replace('\\', '/', str_replace('MVCArchitecture\\', '', $class)) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use MVCArchitecture\Models\UserModel;
use MVCArchitecture\Views\UserListView;
use MVCArchitecture\Views\UserFormView;
use MVCArchitecture\Controllers\UserController;

// Create instances of the MVC components
$model = new UserModel();
$listView = new UserListView();
$formView = new UserFormView();
$controller = new UserController($model, $listView, $formView);

echo "=== MVC Architecture Example ===\n";
echo "This example demonstrates the Model-View-Controller (MVC) architectural pattern.\n";
echo "It shows how to structure a user management system following the MVC approach.\n\n";

// Demonstrate the MVC pattern
echo "STEP 1: Show all users (initial state)\n";
$controller->showUsers();

echo "STEP 2: Show the user creation form\n";
$controller->showCreateForm();

echo "STEP 3: Create a new user\n";
$controller->createUser('Alice Johnson', 'alice.johnson@example.com', 'password789');

echo "STEP 4: Show the edit form for user with ID 1\n";
$controller->showEditForm(1);

echo "STEP 5: Update user with ID 1\n";
$controller->updateUser(1, [
    'name' => 'John Doe Updated',
    'email' => 'john.updated@example.com'
]);

echo "STEP 6: Authenticate a user\n";
$controller->authenticateUser('john.updated@example.com', 'password123');
echo "\n";
$controller->authenticateUser('invalid@example.com', 'wrongpassword');

echo "STEP 7: Delete user with ID 2\n";
$controller->deleteUser(2);

echo "STEP 8: Try to delete a non-existent user\n";
$controller->deleteUser(99);

echo "\nThis example demonstrates the key aspects of the MVC pattern:\n";
echo "1. Model (UserModel.php): Manages data and business logic\n";
echo "2. View (UserListView.php, UserFormView.php): Displays data to the user\n";
echo "3. Controller (UserController.php): Handles user input and updates the model and view\n";
echo "\nThe flow of the application follows the MVC pattern:\n";
echo "1. User interacts with the View\n";
echo "2. Controller receives input and updates the Model\n";
echo "3. Model updates its state and notifies observers (Views)\n";
echo "4. Views update themselves based on the Model's state\n";
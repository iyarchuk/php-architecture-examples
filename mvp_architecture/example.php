<?php

// Include autoloader (in a real application, you would use Composer's autoloader)
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $file = __DIR__ . '/' . str_replace('\\', '/', str_replace('MVPArchitecture\\', '', $class)) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use MVPArchitecture\Models\UserModel;
use MVPArchitecture\Views\UserView;
use MVPArchitecture\Presenters\UserPresenter;

// Create instances of the MVP components
$model = new UserModel();
$view = new UserView();
$presenter = new UserPresenter($model, $view);

echo "=== MVP Architecture Example ===\n";
echo "This example demonstrates the Model-View-Presenter (MVP) architectural pattern.\n";
echo "It shows how to structure a user management system following the MVP approach.\n\n";

// Demonstrate the MVP pattern
echo "STEP 1: Show all users (initial state)\n";
$presenter->loadUsers();

echo "STEP 2: Show the user creation form\n";
$presenter->showCreateForm();

echo "STEP 3: Create a new user\n";
$view->simulateCreateUserInput('Alice Johnson', 'alice.johnson@example.com', 'password789');
$presenter->createUser();

echo "STEP 4: Show the edit form for user with ID 1\n";
$presenter->showEditForm(1);

echo "STEP 5: Update user with ID 1\n";
$view->simulateUpdateUserInput(1, [
    'name' => 'John Doe Updated',
    'email' => 'john.updated@example.com'
]);
$presenter->updateUser();

echo "STEP 6: Authenticate a user\n";
$presenter->authenticateUser('john.updated@example.com', 'password123');
echo "\n";
$presenter->authenticateUser('invalid@example.com', 'wrongpassword');

echo "STEP 7: Delete user with ID 2\n";
$view->simulateDeleteUserInput(2);
$presenter->deleteUser();

echo "STEP 8: Try to delete a non-existent user\n";
$view->simulateDeleteUserInput(99);
$presenter->deleteUser();

echo "\nThis example demonstrates the key aspects of the MVP pattern:\n";
echo "1. Model (UserModel.php): Manages data and business logic\n";
echo "2. View (UserView.php): Displays data to the user and captures user input\n";
echo "3. Presenter (UserPresenter.php): Acts as a mediator between the Model and View\n";
echo "\nThe flow of the application follows the MVP pattern:\n";
echo "1. User interacts with the View\n";
echo "2. View forwards the action to the Presenter\n";
echo "3. Presenter updates the Model based on the action\n";
echo "4. Presenter retrieves data from the Model\n";
echo "5. Presenter formats the data and updates the View\n";
echo "6. View displays the formatted data to the user\n";
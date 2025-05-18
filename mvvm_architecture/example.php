<?php

// Include autoloader (in a real application, you would use Composer's autoloader)
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $file = __DIR__ . '/' . str_replace('\\', '/', str_replace('MVVMArchitecture\\', '', $class)) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use MVVMArchitecture\Models\UserModel;
use MVVMArchitecture\ViewModels\UserViewModel;
use MVVMArchitecture\Views\UserView;

// Create instances of the MVVM components
$model = new UserModel();
$viewModel = new UserViewModel($model);
$view = new UserView($viewModel);

echo "=== MVVM Architecture Example ===\n";
echo "This example demonstrates the Model-View-ViewModel (MVVM) architectural pattern.\n";
echo "It shows how to structure a user management system following the MVVM approach.\n\n";

// Demonstrate the MVVM pattern
echo "STEP 1: Show all users (initial state)\n";
$view->render();

echo "STEP 2: Select a user\n";
$view->simulateSelectUserInput(1);

echo "STEP 3: Create a new user\n";
$view->simulateCreateUserInput('Alice Johnson', 'alice.johnson@example.com', 'password789');

echo "STEP 4: Update user with ID 1\n";
$view->simulateUpdateUserInput(1, [
    'name' => 'John Doe Updated',
    'email' => 'john.updated@example.com'
]);

echo "STEP 5: Authenticate a user\n";
$view->simulateAuthenticateInput('john.updated@example.com', 'password123');
echo "\n";
$view->simulateAuthenticateInput('invalid@example.com', 'wrongpassword');

echo "STEP 6: Delete user with ID 2\n";
$view->simulateDeleteUserInput(2);

echo "STEP 7: Try to delete a non-existent user\n";
$view->simulateDeleteUserInput(99);

echo "\nThis example demonstrates the key aspects of the MVVM pattern:\n";
echo "1. Model (UserModel.php): Manages data and business logic\n";
echo "2. View (UserView.php): Displays data to the user and captures user input\n";
echo "3. ViewModel (UserViewModel.php): Acts as a mediator between the Model and View\n";
echo "\nThe flow of the application follows the MVVM pattern:\n";
echo "1. User interacts with the View\n";
echo "2. View forwards the action to the ViewModel\n";
echo "3. ViewModel updates the Model based on the action\n";
echo "4. Model updates its state and notifies observers (including the ViewModel)\n";
echo "5. ViewModel updates its state and notifies the View\n";
echo "6. View updates itself based on the ViewModel's state\n";
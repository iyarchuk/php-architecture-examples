<?php

// Include autoloader (in a real application, you would use Composer's autoloader)
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $file = __DIR__ . '/' . str_replace('\\', '/', str_replace('DomainDrivenDesign\\', '', $class)) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use DomainDrivenDesign\Domain\Services\UserService;
use DomainDrivenDesign\Application\Services\UserApplicationService;
use DomainDrivenDesign\Infrastructure\Persistence\InMemoryUserRepository;
use DomainDrivenDesign\Presentation\Controllers\UserController;
use DomainDrivenDesign\Presentation\Views\UserView;

echo "=== Domain-Driven Design (DDD) Architecture Example ===\n";
echo "This example demonstrates the Domain-Driven Design architectural pattern.\n";
echo "It shows how to structure a user management system following the DDD approach.\n\n";

// Initialize components
echo "STEP 1: Initializing components\n";
$userRepository = new InMemoryUserRepository();
$userService = new UserService($userRepository);
$userApplicationService = new UserApplicationService($userService);
$userController = new UserController($userApplicationService);
$userView = new UserView();
echo "Components initialized successfully.\n\n";

// Register users
echo "STEP 2: Registering users\n";
$response = $userController->register([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 'Password123'
]);
$userView->displayResponse($response);

$response = $userController->register([
    'name' => 'Jane Smith',
    'email' => 'jane.smith@example.com',
    'password' => 'Password456'
]);
$userView->displayResponse($response);

// Try to register a user with an existing email
echo "STEP 3: Trying to register a user with an existing email\n";
$response = $userController->register([
    'name' => 'Another User',
    'email' => 'john.doe@example.com', // Same email as John Doe
    'password' => 'Password789'
]);
$userView->displayResponse($response);

// Get all users
echo "STEP 4: Getting all users\n";
$response = $userController->getAllUsers();
$userView->displayResponse($response);

// Get a specific user
echo "STEP 5: Getting a specific user\n";
// Note: In a real application, you would get the user ID from the registration response
// For this example, we'll use a hardcoded ID
$userId = $response['data'][0]['id'];
$response = $userController->getUser($userId);
$userView->displayResponse($response);

// Update a user
echo "STEP 6: Updating a user\n";
$response = $userController->updateUser($userId, [
    'name' => 'John Doe Updated',
    'email' => 'john.updated@example.com'
]);
$userView->displayResponse($response);

// Authenticate a user
echo "STEP 7: Authenticating a user\n";
$response = $userController->authenticate([
    'email' => 'john.updated@example.com',
    'password' => 'Password123'
]);
$userView->displayResponse($response);

// Try to authenticate with wrong password
echo "STEP 8: Trying to authenticate with wrong password\n";
$response = $userController->authenticate([
    'email' => 'john.updated@example.com',
    'password' => 'WrongPassword'
]);
$userView->displayResponse($response);

// Delete a user
echo "STEP 9: Deleting a user\n";
$response = $userController->deleteUser($userId);
$userView->displayResponse($response);

// Get all users after deletion
echo "STEP 10: Getting all users after deletion\n";
$response = $userController->getAllUsers();
$userView->displayResponse($response);

echo "\nThis example demonstrates the key aspects of the Domain-Driven Design pattern:\n";
echo "1. Domain Layer: Contains the business logic and domain entities\n";
echo "   - Entities (User)\n";
echo "   - Value Objects (UserId, Email, Password)\n";
echo "   - Domain Services (UserService)\n";
echo "   - Aggregates (UserAggregate)\n";
echo "2. Application Layer: Orchestrates the execution of domain logic\n";
echo "   - Application Services (UserApplicationService)\n";
echo "   - DTOs (UserDTO)\n";
echo "3. Infrastructure Layer: Provides technical capabilities\n";
echo "   - Repositories (UserRepository interface)\n";
echo "   - Persistence (InMemoryUserRepository)\n";
echo "4. Presentation Layer: Handles user interaction\n";
echo "   - Controllers (UserController)\n";
echo "   - Views (UserView)\n";
echo "\nThe flow of the application follows the DDD pattern:\n";
echo "1. User interacts with the Presentation Layer\n";
echo "2. Presentation Layer sends commands to the Application Layer\n";
echo "3. Application Layer uses Domain Layer to execute business logic\n";
echo "4. Domain Layer may use Infrastructure Layer for persistence\n";
echo "5. Results flow back through the layers to the user\n";

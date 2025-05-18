<?php

// Autoload classes (in a real application, you would use Composer's autoloader)
spl_autoload_register(function ($class) {
    $prefix = 'CleanArchitecture\\';
    $base_dir = __DIR__ . '/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Import classes
use CleanArchitecture\Domain\User;
use CleanArchitecture\Infrastructure\InMemoryUserRepository;
use CleanArchitecture\Application\CreateUserUseCase;
use CleanArchitecture\Application\CreateUserRequest;
use CleanArchitecture\Application\GetUserUseCase;
use CleanArchitecture\Application\GetUserRequest;
use CleanArchitecture\Presentation\UserController;

// Create dependencies
$userRepository = new InMemoryUserRepository();
$createUserUseCase = new CreateUserUseCase($userRepository);
$getUserUseCase = new GetUserUseCase($userRepository);
$userController = new UserController($createUserUseCase, $getUserUseCase);

// Example 1: Create a user
echo "Example 1: Creating a user\n";
$createUserResult = $userController->createUser([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 'password123'
]);

// Print the result
echo "Result: " . ($createUserResult['success'] ? 'Success' : 'Failure') . "\n";
echo "Message: " . ($createUserResult['message'] ?? 'No message') . "\n";
if (isset($createUserResult['data'])) {
    echo "User ID: " . $createUserResult['data']['id'] . "\n";
    echo "User Name: " . $createUserResult['data']['name'] . "\n";
    echo "User Email: " . $createUserResult['data']['email'] . "\n";
}
echo "\n";

// Example 2: Get the user we just created
echo "Example 2: Getting a user\n";
$userId = $createUserResult['data']['id'];
$getUserResult = $userController->getUser($userId);

// Print the result
echo "Result: " . ($getUserResult['success'] ? 'Success' : 'Failure') . "\n";
if (isset($getUserResult['data'])) {
    echo "User ID: " . $getUserResult['data']['id'] . "\n";
    echo "User Name: " . $getUserResult['data']['name'] . "\n";
    echo "User Email: " . $getUserResult['data']['email'] . "\n";
} else {
    echo "Message: " . ($getUserResult['message'] ?? 'No message') . "\n";
}
echo "\n";

// Example 3: Try to get a non-existent user
echo "Example 3: Getting a non-existent user\n";
$nonExistentUserId = 999;
$getNonExistentUserResult = $userController->getUser($nonExistentUserId);

// Print the result
echo "Result: " . ($getNonExistentUserResult['success'] ? 'Success' : 'Failure') . "\n";
echo "Message: " . ($getNonExistentUserResult['message'] ?? 'No message') . "\n";
echo "\n";

// Example 4: Try to create a user with invalid data
echo "Example 4: Creating a user with invalid data\n";
$createInvalidUserResult = $userController->createUser([
    'name' => 'Jane Doe',
    'email' => 'invalid-email',
    'password' => 'password123'
]);

// Print the result
echo "Result: " . ($createInvalidUserResult['success'] ? 'Success' : 'Failure') . "\n";
echo "Message: " . ($createInvalidUserResult['message'] ?? 'No message') . "\n";
echo "\n";

// Example 5: Try to create a user with missing data
echo "Example 5: Creating a user with missing data\n";
$createMissingDataUserResult = $userController->createUser([
    'name' => 'Jane Doe',
    'email' => 'jane.doe@example.com'
]);

// Print the result
echo "Result: " . ($createMissingDataUserResult['success'] ? 'Success' : 'Failure') . "\n";
echo "Message: " . ($createMissingDataUserResult['message'] ?? 'No message') . "\n";
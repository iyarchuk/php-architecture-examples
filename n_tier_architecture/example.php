<?php

// Autoload classes (in a real application, you would use Composer's autoloader)
spl_autoload_register(function ($class) {
    $prefix = 'NTierArchitecture\\';
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
use NTierArchitecture\Presentation\UserController;

// Create a controller
$userController = new UserController();

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

// Example 3: Get all users
echo "Example 3: Getting all users\n";
$getAllUsersResult = $userController->getAllUsers();

// Print the result
echo "Result: " . ($getAllUsersResult['success'] ? 'Success' : 'Failure') . "\n";
echo "Number of users: " . count($getAllUsersResult['data']) . "\n";
echo "\n";

// Example 4: Update a user
echo "Example 4: Updating a user\n";
$updateUserResult = $userController->updateUser($userId, [
    'name' => 'John Updated',
    'email' => 'john.updated@example.com'
]);

// Print the result
echo "Result: " . ($updateUserResult['success'] ? 'Success' : 'Failure') . "\n";
echo "Message: " . ($updateUserResult['message'] ?? 'No message') . "\n";
if (isset($updateUserResult['data'])) {
    echo "User ID: " . $updateUserResult['data']['id'] . "\n";
    echo "User Name: " . $updateUserResult['data']['name'] . "\n";
    echo "User Email: " . $updateUserResult['data']['email'] . "\n";
}
echo "\n";

// Example 5: Authenticate a user
echo "Example 5: Authenticating a user\n";
$authenticateUserResult = $userController->authenticateUser([
    'email' => 'john.updated@example.com',
    'password' => 'password123'
]);

// Print the result
echo "Result: " . ($authenticateUserResult['success'] ? 'Success' : 'Failure') . "\n";
echo "Message: " . ($authenticateUserResult['message'] ?? 'No message') . "\n";
if (isset($authenticateUserResult['data'])) {
    echo "User ID: " . $authenticateUserResult['data']['id'] . "\n";
    echo "User Name: " . $authenticateUserResult['data']['name'] . "\n";
    echo "User Email: " . $authenticateUserResult['data']['email'] . "\n";
}
echo "\n";

// Example 6: Delete a user
echo "Example 6: Deleting a user\n";
$deleteUserResult = $userController->deleteUser($userId);

// Print the result
echo "Result: " . ($deleteUserResult['success'] ? 'Success' : 'Failure') . "\n";
echo "Message: " . ($deleteUserResult['message'] ?? 'No message') . "\n";
echo "\n";

// Example 7: Try to get a deleted user
echo "Example 7: Getting a deleted user\n";
$getDeletedUserResult = $userController->getUser($userId);

// Print the result
echo "Result: " . ($getDeletedUserResult['success'] ? 'Success' : 'Failure') . "\n";
echo "Message: " . ($getDeletedUserResult['message'] ?? 'No message') . "\n";
echo "\n";

// Example 8: Try to create a user with invalid data
echo "Example 8: Creating a user with invalid data\n";
$createInvalidUserResult = $userController->createUser([
    'name' => 'Jane Doe',
    'email' => 'invalid-email',
    'password' => 'password123'
]);

// Print the result
echo "Result: " . ($createInvalidUserResult['success'] ? 'Success' : 'Failure') . "\n";
echo "Message: " . ($createInvalidUserResult['message'] ?? 'No message') . "\n";
echo "\n";

// Example 9: Try to create a user with missing data
echo "Example 9: Creating a user with missing data\n";
$createMissingDataUserResult = $userController->createUser([
    'name' => 'Jane Doe',
    'email' => 'jane.doe@example.com'
]);

// Print the result
echo "Result: " . ($createMissingDataUserResult['success'] ? 'Success' : 'Failure') . "\n";
echo "Message: " . ($createMissingDataUserResult['message'] ?? 'No message') . "\n";
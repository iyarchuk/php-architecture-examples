<?php

// Include autoloader (in a real application, you would use Composer's autoloader)
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $file = __DIR__ . '/' . str_replace('\\', '/', str_replace('CQRSArchitecture\\', '', $class)) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use CQRSArchitecture\Commands\CreateUserCommand;
use CQRSArchitecture\Commands\UpdateUserCommand;
use CQRSArchitecture\Commands\DeleteUserCommand;
use CQRSArchitecture\Queries\GetUserByIdQuery;
use CQRSArchitecture\Queries\GetAllUsersQuery;
use CQRSArchitecture\Handlers\CommandHandlers\CreateUserHandler;
use CQRSArchitecture\Handlers\CommandHandlers\UpdateUserHandler;
use CQRSArchitecture\Handlers\CommandHandlers\DeleteUserHandler;
use CQRSArchitecture\Handlers\QueryHandlers\GetUserByIdHandler;
use CQRSArchitecture\Handlers\QueryHandlers\GetAllUsersHandler;
use CQRSArchitecture\Models\UserReadModel;
use CQRSArchitecture\EventStore\EventStore;

// Initialize the read model with some sample data
UserReadModel::initialize();

echo "=== CQRS Architecture Example ===\n";
echo "This example demonstrates the Command Query Responsibility Segregation (CQRS) architectural pattern.\n";
echo "It shows how to structure a user management system following the CQRS approach.\n\n";

// Display the initial state
echo "STEP 1: Initial state\n";
$getAllUsersQuery = new GetAllUsersQuery();
$getAllUsersHandler = new GetAllUsersHandler();
$users = $getAllUsersHandler->handle($getAllUsersQuery);

echo "Initial users:\n";
displayUsers($users);

// Create a new user
echo "\nSTEP 2: Create a new user\n";
$createUserCommand = new CreateUserCommand('Alice Johnson', 'alice.johnson@example.com', 'password789');
$createUserHandler = new CreateUserHandler();
$newUserId = $createUserHandler->handle($createUserCommand);

echo "User created with ID: $newUserId\n";

// Get the newly created user
$getUserByIdQuery = new GetUserByIdQuery($newUserId);
$getUserByIdHandler = new GetUserByIdHandler();
$user = $getUserByIdHandler->handle($getUserByIdQuery);

echo "New user details:\n";
displayUser($user);

// Update a user
echo "\nSTEP 3: Update a user\n";
$updateUserCommand = new UpdateUserCommand(1, 'John Doe Updated', 'john.updated@example.com');
$updateUserHandler = new UpdateUserHandler();
$result = $updateUserHandler->handle($updateUserCommand);

echo "User update " . ($result ? "successful" : "failed") . "\n";

// Get the updated user
$getUserByIdQuery = new GetUserByIdQuery(1);
$user = $getUserByIdHandler->handle($getUserByIdQuery);

echo "Updated user details:\n";
displayUser($user);

// Delete a user
echo "\nSTEP 4: Delete a user\n";
$deleteUserCommand = new DeleteUserCommand(2);
$deleteUserHandler = new DeleteUserHandler();
$result = $deleteUserHandler->handle($deleteUserCommand);

echo "User deletion " . ($result ? "successful" : "failed") . "\n";

// Get all users after deletion
$users = $getAllUsersHandler->handle($getAllUsersQuery);

echo "Users after deletion:\n";
displayUsers($users);

// Try to get a deleted user
echo "\nSTEP 5: Try to get a deleted user\n";
$getUserByIdQuery = new GetUserByIdQuery(2);
$user = $getUserByIdHandler->handle($getUserByIdQuery);

echo "Deleted user: " . ($user ? json_encode($user) : "Not found") . "\n";

// Display events from the event store
echo "\nSTEP 6: Display events from the event store\n";
$events = EventStore::getAllEvents();

echo "Events in the event store:\n";
foreach ($events as $event) {
    echo "- " . $event->getType() . " at " . $event->getTimestamp() . "\n";
    echo "  Data: " . json_encode($event->getData()) . "\n";
}

echo "\nThis example demonstrates the key aspects of the CQRS pattern:\n";
echo "1. Separation of read and write operations\n";
echo "2. Command pattern for write operations\n";
echo "3. Query pattern for read operations\n";
echo "4. Event sourcing for tracking state changes\n";
echo "5. Different models for reading and writing\n";

/**
 * Display a list of users
 * 
 * @param array $users
 * @return void
 */
function displayUsers(array $users): void
{
    if (empty($users)) {
        echo "No users found.\n";
        return;
    }
    
    foreach ($users as $user) {
        echo "ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}, Created: {$user['created_at']}\n";
    }
}

/**
 * Display a single user
 * 
 * @param array|null $user
 * @return void
 */
function displayUser(?array $user): void
{
    if (!$user) {
        echo "User not found.\n";
        return;
    }
    
    echo "ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}, Created: {$user['created_at']}\n";
}
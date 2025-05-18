<?php

// Include autoloader (in a real application, you would use Composer's autoloader)
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $file = __DIR__ . '/' . str_replace('\\', '/', str_replace('ClientServerArchitecture\\', '', $class)) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use ClientServerArchitecture\Client\Client;
use ClientServerArchitecture\Client\UserInterface;
use ClientServerArchitecture\Server\Server;

echo "=== Client-Server Architecture Example ===\n";
echo "This example demonstrates the Client-Server architectural pattern.\n";
echo "It shows how to structure a user management system following the Client-Server approach.\n\n";

// Create the server
echo "STEP 1: Starting the server\n";
$server = new Server();
echo $server->start() . "\n\n";

// Create the client
echo "STEP 2: Creating the client\n";
$client = new Client($server);
echo "Client created and connected to the server.\n\n";

// Create the user interface
echo "STEP 3: Creating the user interface\n";
$ui = new UserInterface($client);
$ui->displayWelcome();

// Display initial users
echo "STEP 4: Displaying initial users\n";
$ui->displayUsers();

// Create a new user
echo "STEP 5: Creating a new user\n";
$ui->createUser('Alice Johnson', 'alice.johnson@example.com', 'password789');

// Display users after creation
echo "STEP 6: Displaying users after creation\n";
$ui->displayUsers();

// Get a user by ID
echo "STEP 7: Getting a user by ID\n";
$ui->displayUser(1);

// Update a user
echo "STEP 8: Updating a user\n";
$ui->updateUser(1, [
    'name' => 'John Doe Updated',
    'email' => 'john.updated@example.com'
]);

// Display the updated user
echo "STEP 9: Displaying the updated user\n";
$ui->displayUser(1);

// Authenticate a user
echo "STEP 10: Authenticating a user\n";
$ui->authenticateUser('john.updated@example.com', 'password123');
echo "\n";
$ui->authenticateUser('invalid@example.com', 'wrongpassword');

// Delete a user
echo "STEP 11: Deleting a user\n";
$ui->deleteUser(2);

// Display users after deletion
echo "STEP 12: Displaying users after deletion\n";
$ui->displayUsers();

// Stop the server
echo "STEP 13: Stopping the server\n";
echo $server->stop() . "\n\n";

echo "This example demonstrates the key aspects of the Client-Server pattern:\n";
echo "1. Separation of client and server components\n";
echo "2. Client sends requests to the server\n";
echo "3. Server processes requests and returns responses\n";
echo "4. Client displays results to the user\n";
echo "5. Communication follows a defined protocol\n\n";

echo "The flow of the application follows the Client-Server pattern:\n";
echo "1. Client captures user input or generates a request\n";
echo "2. Client sends the request to the server\n";
echo "3. Server receives and validates the request\n";
echo "4. Server processes the request (applies business logic, interacts with data storage)\n";
echo "5. Server generates a response\n";
echo "6. Server sends the response back to the client\n";
echo "7. Client receives and processes the response\n";
echo "8. Client updates its user interface based on the response\n";
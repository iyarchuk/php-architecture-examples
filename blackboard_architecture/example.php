<?php

// Include autoloader (in a real application, you would use Composer's autoloader)
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $file = __DIR__ . '/' . str_replace('\\', '/', str_replace('BlackboardArchitecture\\', '', $class)) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use BlackboardArchitecture\Blackboard\Blackboard;
use BlackboardArchitecture\Control\Controller;
use BlackboardArchitecture\KnowledgeSources\NameAnalyzer;
use BlackboardArchitecture\KnowledgeSources\EmailValidator;
use BlackboardArchitecture\KnowledgeSources\PasswordStrengthChecker;
use BlackboardArchitecture\KnowledgeSources\UserProfileCompleter;

echo "=== Blackboard Architecture Example ===\n";
echo "This example demonstrates the Blackboard architectural pattern.\n";
echo "It shows how to structure a user registration system following the Blackboard approach.\n\n";

// Create the blackboard
$blackboard = new Blackboard();

// Create the knowledge sources
$nameAnalyzer = new NameAnalyzer();
$emailValidator = new EmailValidator();
$passwordStrengthChecker = new PasswordStrengthChecker();
$userProfileCompleter = new UserProfileCompleter();

// Create the controller
$controller = new Controller($blackboard);

// Add knowledge sources to the controller
$controller->addKnowledgeSources([
    $nameAnalyzer,
    $emailValidator,
    $passwordStrengthChecker,
    $userProfileCompleter
]);

// Example 1: Valid user registration
echo "EXAMPLE 1: Valid User Registration\n";
echo "-----------------------------------\n";

// Place the initial problem on the blackboard
$blackboard->setMultiple([
    'name' => 'John Smith',
    'email' => 'john.smith@example.com',
    'password' => 'Secure123!'
], 'Example');

// Run the controller
$result = $controller->run();

// Display the result
echo "Registration " . ($result ? "successful" : "failed") . "\n\n";

// Display the final state of the blackboard
echo "Final User Profile:\n";
$userProfile = $blackboard->get('user_profile', []);
foreach ($userProfile as $key => $value) {
    if (is_array($value)) {
        echo "- $key: " . json_encode($value) . "\n";
    } else {
        echo "- $key: $value\n";
    }
}

// Display recommendations if any
if ($blackboard->has('user_recommendations')) {
    echo "\nRecommendations:\n";
    foreach ($blackboard->get('user_recommendations') as $recommendation) {
        echo "- $recommendation\n";
    }
}

echo "\nExecution Log:\n";
foreach ($controller->getExecutionLog() as $logEntry) {
    echo "[{$logEntry['timestamp']}] {$logEntry['message']}\n";
}

// Example 2: Invalid user registration
echo "\n\nEXAMPLE 2: Invalid User Registration\n";
echo "-------------------------------------\n";

// Create a new blackboard for the second example
$blackboard2 = new Blackboard();
$controller2 = new Controller($blackboard2);
$controller2->addKnowledgeSources([
    $nameAnalyzer,
    $emailValidator,
    $passwordStrengthChecker,
    $userProfileCompleter
]);

// Place the initial problem on the blackboard
$blackboard2->setMultiple([
    'name' => 'J', // Too short
    'email' => 'invalid-email', // Invalid email
    'password' => '123' // Too short and weak
], 'Example');

// Run the controller
$result2 = $controller2->run();

// Display the result
echo "Registration " . ($result2 ? "successful" : "failed") . "\n\n";

// Display errors if any
if ($blackboard2->has('registration_errors')) {
    echo "Registration Errors:\n";
    foreach ($blackboard2->get('registration_errors') as $error) {
        echo "- $error\n";
    }
}

echo "\nExecution Log:\n";
foreach ($controller2->getExecutionLog() as $logEntry) {
    echo "[{$logEntry['timestamp']}] {$logEntry['message']}\n";
}

// Example 3: Partial information
echo "\n\nEXAMPLE 3: Incremental Problem Solving\n";
echo "---------------------------------------\n";

// Create a new blackboard for the third example
$blackboard3 = new Blackboard();
$controller3 = new Controller($blackboard3);
$controller3->addKnowledgeSources([
    $nameAnalyzer,
    $emailValidator,
    $passwordStrengthChecker,
    $userProfileCompleter
]);

// Start with just a name
echo "Step 1: Adding just the name\n";
$blackboard3->set('name', 'Alice Johnson', 'Example');
$controller3->run();

// Display the current state
echo "\nCurrent Blackboard State:\n";
foreach ($blackboard3->getState() as $key => $value) {
    if (is_array($value)) {
        echo "- $key: " . json_encode($value) . "\n";
    } else {
        echo "- $key: $value\n";
    }
}

// Add email
echo "\nStep 2: Adding email\n";
$blackboard3->set('email', 'alice.johnson@gmail.com', 'Example');
$controller3->run();

// Add password
echo "\nStep 3: Adding password\n";
$blackboard3->set('password', 'StrongP@ssw0rd', 'Example');
$result3 = $controller3->run();

// Display the final result
echo "\nFinal Registration " . ($result3 ? "successful" : "failed") . "\n\n";

// Display the final user profile
echo "Final User Profile:\n";
$userProfile3 = $blackboard3->get('user_profile', []);
foreach ($userProfile3 as $key => $value) {
    if (is_array($value)) {
        echo "- $key: " . json_encode($value) . "\n";
    } else {
        echo "- $key: $value\n";
    }
}

echo "\nThis example demonstrates the key aspects of the Blackboard pattern:\n";
echo "1. Multiple specialized knowledge sources working together\n";
echo "2. Incremental problem solving\n";
echo "3. Opportunistic activation of knowledge sources\n";
echo "4. Central data structure (blackboard) for sharing information\n";
echo "5. Control component orchestrating the problem-solving process\n";
<?php

// Autoload classes (in a real application, you would use Composer's autoloader)
spl_autoload_register(function ($class) {
    $prefix = 'ServerlessArchitecture\\';
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
use ServerlessArchitecture\Config\ServerlessConfig;
use ServerlessArchitecture\Functions\UserFunction;
use ServerlessArchitecture\Functions\ProductFunction;
use ServerlessArchitecture\Events\DatabaseEventTrigger;
use ServerlessArchitecture\Events\ScheduledEventTrigger;
use ServerlessArchitecture\API\APIGateway;
use ServerlessArchitecture\Services\StorageService;
use ServerlessArchitecture\Services\DatabaseService;

// Initialize configuration
$config = ServerlessConfig::getInstance();

// Example 1: Using functions
echo "Example 1: Using functions\n";

// Create a user function
$userFunction = new UserFunction();

// Simulate an API Gateway event for creating a user
$createUserEvent = [
    'body' => [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com'
    ],
    'headers' => [
        'Content-Type' => 'application/json'
    ]
];

// Create a context object (in AWS Lambda, this would contain runtime information)
$context = new stdClass();
$context->functionName = 'createUser';
$context->functionVersion = '1.0';
$context->memoryLimitInMB = 128;
$context->awsRequestId = uniqid();

// Invoke the function
$createUserResult = $userFunction->createUser($createUserEvent, $context);

// Print the result
echo "Create User Result:\n";
echo "Status Code: " . $createUserResult['statusCode'] . "\n";
echo "Body: " . $createUserResult['body'] . "\n\n";

// Example 2: Using event triggers
echo "Example 2: Using event triggers\n";

// Create a database event trigger
$databaseEventTrigger = new DatabaseEventTrigger([
    'tableName' => 'users',
    'operation' => 'INSERT',
    'functionName' => 'processUserCreation'
]);

// Simulate a database event
$databaseEvent = [
    'tableName' => 'users',
    'operation' => 'INSERT',
    'records' => [
        [
            'id' => '123',
            'name' => 'John Doe',
            'email' => 'john.doe@example.com'
        ]
    ]
];

// Process the event
$databaseEventResult = $databaseEventTrigger->processEvent($databaseEvent);

// Print the result
echo "Database Event Result:\n";
echo "Success: " . ($databaseEventResult['success'] ? 'true' : 'false') . "\n";
echo "Message: " . $databaseEventResult['message'] . "\n";
if (isset($databaseEventResult['data'])) {
    echo "Function Name: " . $databaseEventResult['data']['functionName'] . "\n";
    echo "Records Count: " . count($databaseEventResult['data']['records']) . "\n";
}
echo "\n";

// Example 3: Using API Gateway
echo "Example 3: Using API Gateway\n";

// Create an API Gateway
$apiGateway = new APIGateway([
    'name' => 'UserAPI',
    'version' => 'v1',
    'basePath' => '/api'
]);

// Add routes
$apiGateway->addRoute('GET', '/users/{userId}', 'getUserFunction')
           ->addRoute('POST', '/users', 'createUserFunction', ['auth' => true])
           ->addRoute('DELETE', '/users/{userId}', 'deleteUserFunction', ['auth' => true]);

// Simulate an API request
$apiRequest = [
    'method' => 'GET',
    'path' => '/users/123',
    'headers' => [
        'Content-Type' => 'application/json'
    ],
    'queryParams' => [
        'include' => 'profile'
    ]
];

// Process the request
$apiResponse = $apiGateway->processRequest(
    $apiRequest['method'],
    $apiRequest['path'],
    $apiRequest['headers'],
    $apiRequest['queryParams']
);

// Print the result
echo "API Gateway Result:\n";
echo "Status Code: " . $apiResponse['statusCode'] . "\n";
echo "Body: " . $apiResponse['body'] . "\n\n";

// Example 4: Using cloud services
echo "Example 4: Using cloud services\n";

// Create a database service
$databaseService = new DatabaseService([
    'tableName' => 'users'
]);

// Put an item in the database
$putItemResult = $databaseService->putItem([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'age' => 30
]);

// Get the item ID
$itemId = $putItemResult['data']['item']['id'];

// Get the item from the database
$getItemResult = $databaseService->getItem($itemId);

// Print the result
echo "Database Service Result:\n";
echo "Put Item Success: " . ($putItemResult['success'] ? 'true' : 'false') . "\n";
echo "Get Item Success: " . ($getItemResult['success'] ? 'true' : 'false') . "\n";
if (isset($getItemResult['data']['item'])) {
    echo "Item Name: " . $getItemResult['data']['item']['name'] . "\n";
    echo "Item Email: " . $getItemResult['data']['item']['email'] . "\n";
}
echo "\n";

// Create a storage service
$storageService = new StorageService([
    'bucketName' => 'user-files'
]);

// Upload a file
$uploadResult = $storageService->uploadFile(
    'users/123/profile.json',
    json_encode(['name' => 'John Doe', 'bio' => 'Software Developer']),
    ['contentType' => 'application/json']
);

// Download the file
$downloadResult = $storageService->downloadFile('users/123/profile.json');

// Print the result
echo "Storage Service Result:\n";
echo "Upload Success: " . ($uploadResult['success'] ? 'true' : 'false') . "\n";
echo "Download Success: " . ($downloadResult['success'] ? 'true' : 'false') . "\n";
if (isset($downloadResult['data']['content'])) {
    echo "File Content: " . $downloadResult['data']['content'] . "\n";
}
echo "\n";

// Example 5: Using scheduled events
echo "Example 5: Using scheduled events\n";

// Create a scheduled event trigger
$scheduledEventTrigger = new ScheduledEventTrigger([
    'name' => 'DailyReport',
    'schedule' => 'cron(0 12 * * ? *)',
    'functionName' => 'generateDailyReport'
]);

// Process the scheduled event
$scheduledEventResult = $scheduledEventTrigger->processEvent();

// Print the result
echo "Scheduled Event Result:\n";
echo "Success: " . ($scheduledEventResult['success'] ? 'true' : 'false') . "\n";
echo "Message: " . $scheduledEventResult['message'] . "\n";
if (isset($scheduledEventResult['data'])) {
    echo "Event Name: " . $scheduledEventResult['data']['name'] . "\n";
    echo "Function Name: " . $scheduledEventResult['data']['functionName'] . "\n";
    echo "Schedule: " . $scheduledEventResult['data']['schedule'] . "\n";
}
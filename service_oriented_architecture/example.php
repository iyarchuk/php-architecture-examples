<?php

// Autoload classes (in a real application, you would use Composer's autoloader)
spl_autoload_register(function ($class) {
    $prefix = 'ServiceOrientedArchitecture\\';
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
use ServiceOrientedArchitecture\Contracts\UserServiceInterface;
use ServiceOrientedArchitecture\Contracts\ProductServiceInterface;
use ServiceOrientedArchitecture\Contracts\ServiceRegistryInterface;
use ServiceOrientedArchitecture\Services\UserService;
use ServiceOrientedArchitecture\Services\ProductService;
use ServiceOrientedArchitecture\Registry\ServiceRegistry;
use ServiceOrientedArchitecture\Client\ServiceClient;
use ServiceOrientedArchitecture\Orchestration\ServiceOrchestrator;

// Example 1: Setting up the Service Registry
echo "Example 1: Setting up the Service Registry\n";

// Create a service registry
$registry = new ServiceRegistry();

// Register services
$registry->registerService('user-service', 'http://user-service.example.com', [
    'version' => '1.0',
    'description' => 'User management service'
]);

$registry->registerService('product-service', 'http://product-service.example.com', [
    'version' => '1.0',
    'description' => 'Product management service'
]);

// List all registered services
$services = $registry->listServices();
echo "Registered services:\n";
foreach ($services as $service) {
    echo "- {$service['name']} ({$service['url']})\n";
    echo "  Version: {$service['metadata']['version']}\n";
    echo "  Description: {$service['metadata']['description']}\n";
}
echo "\n";

// Example 2: Using the Service Client
echo "Example 2: Using the Service Client\n";

// Create a service client
$client = new ServiceClient($registry);

// Discover a service
$userService = $client->discoverService('user-service');
echo "Discovered user service: {$userService['name']} ({$userService['url']})\n";

// Call a service method
try {
    $createUserResult = $client->call('user-service', 'createUser', [
        'name' => 'Alice Johnson',
        'email' => 'alice.johnson@example.com'
    ]);
    
    echo "Created user: {$createUserResult['data']['name']} ({$createUserResult['data']['email']})\n";
    echo "User ID: {$createUserResult['data']['id']}\n";
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}

// Call another service method
try {
    $getProductResult = $client->call('product-service', 'getProduct', [
        'productId' => '1'
    ]);
    
    echo "Retrieved product: {$getProductResult['data']['name']}\n";
    echo "Price: \${$getProductResult['data']['price']}\n";
    echo "Description: {$getProductResult['data']['description']}\n";
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}
echo "\n";

// Example 3: Using the Service Orchestrator
echo "Example 3: Using the Service Orchestrator\n";

// Create a service orchestrator
$orchestrator = new ServiceOrchestrator($client);

// Create a user with products
$createUserWithProductsResult = $orchestrator->createUserWithProducts(
    [
        'name' => 'Bob Smith',
        'email' => 'bob.smith@example.com'
    ],
    ['1', '2'] // Product IDs
);

if ($createUserWithProductsResult['success']) {
    echo "Created user with products:\n";
    echo "User: {$createUserWithProductsResult['data']['user']['name']} ({$createUserWithProductsResult['data']['user']['email']})\n";
    echo "Products:\n";
    foreach ($createUserWithProductsResult['data']['products'] as $product) {
        echo "- {$product['name']} (\${$product['price']})\n";
    }
} else {
    echo "Error: {$createUserWithProductsResult['message']}\n";
}
echo "\n";

// Process an order
$processOrderResult = $orchestrator->processOrder(
    '1', // User ID
    [
        ['productId' => '1', 'quantity' => 2],
        ['productId' => '3', 'quantity' => 1]
    ]
);

if ($processOrderResult['success']) {
    echo "Processed order:\n";
    echo "Order ID: {$processOrderResult['data']['order']['id']}\n";
    echo "User ID: {$processOrderResult['data']['order']['userId']}\n";
    echo "Total amount: \${$processOrderResult['data']['order']['totalAmount']}\n";
    echo "Items:\n";
    foreach ($processOrderResult['data']['order']['items'] as $item) {
        echo "- {$item['product']['name']} x {$item['quantity']} = \${$item['itemTotal']}\n";
    }
} else {
    echo "Error: {$processOrderResult['message']}\n";
}
echo "\n";

// Search products and get user details
$searchResult = $orchestrator->searchProductsAndGetUserDetails('laptop', '1');

if ($searchResult['success']) {
    echo "Search results for 'laptop':\n";
    foreach ($searchResult['data']['products'] as $product) {
        echo "- {$product['name']} (\${$product['price']})\n";
        echo "  {$product['description']}\n";
    }
    echo "User details:\n";
    echo "Name: {$searchResult['data']['user']['name']}\n";
    echo "Email: {$searchResult['data']['user']['email']}\n";
} else {
    echo "Error: {$searchResult['message']}\n";
}
echo "\n";

// Example 4: Direct usage of services
echo "Example 4: Direct usage of services\n";

// Create service instances directly
$userService = new UserService();
$productService = new ProductService();

// Use the user service
$listUsersResult = $userService->listUsers();
echo "Users:\n";
foreach ($listUsersResult['data'] as $user) {
    echo "- {$user['name']} ({$user['email']})\n";
}

// Use the product service
$listProductsResult = $productService->listProducts();
echo "Products:\n";
foreach ($listProductsResult['data'] as $product) {
    echo "- {$product['name']} (\${$product['price']})\n";
}

// Search for products
$searchProductsResult = $productService->searchProducts('phone');
echo "Search results for 'phone':\n";
if ($searchProductsResult['success'] && !empty($searchProductsResult['data'])) {
    foreach ($searchProductsResult['data'] as $product) {
        echo "- {$product['name']} (\${$product['price']})\n";
        echo "  {$product['description']}\n";
    }
} else {
    echo "No products found.\n";
}
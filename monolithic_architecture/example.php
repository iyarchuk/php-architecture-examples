<?php

// Include autoloader (in a real application, you would use Composer's autoloader)
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $file = __DIR__ . '/' . str_replace('\\', '/', str_replace('MonolithicArchitecture\\', '', $class)) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

echo "=== Monolithic Architecture Example ===\n";
echo "This example demonstrates the Monolithic architectural pattern.\n";
echo "It shows how to structure an e-commerce system following the Monolithic approach.\n\n";

// STEP 1: Initialize the application
echo "STEP 1: Initializing the application\n";
// In a monolithic application, we initialize the entire application at once
// All components are tightly integrated and share the same codebase
echo "Application initialized.\n\n";

// STEP 2: Set up the database connection
echo "STEP 2: Setting up the database connection\n";
// In a monolithic application, we typically have a single database for all data
echo "Database connection established.\n\n";

// STEP 3: Create the controllers
echo "STEP 3: Creating the controllers\n";
// In a monolithic application, controllers handle HTTP requests and delegate to services
$userController = new MonolithicArchitecture\Controllers\UserController();
$productController = new MonolithicArchitecture\Controllers\ProductController();
$orderController = new MonolithicArchitecture\Controllers\OrderController();
$paymentController = new MonolithicArchitecture\Controllers\PaymentController();
echo "Controllers created.\n\n";

// STEP 4: Create the services
echo "STEP 4: Creating the services\n";
// In a monolithic application, services contain the business logic
$userService = new MonolithicArchitecture\Services\UserService();
$productService = new MonolithicArchitecture\Services\ProductService();
$orderService = new MonolithicArchitecture\Services\OrderService();
$paymentService = new MonolithicArchitecture\Services\PaymentService();
echo "Services created.\n\n";

// STEP 5: Create the models
echo "STEP 5: Creating the models\n";
// In a monolithic application, models represent the data and database interactions
$userModel = new MonolithicArchitecture\Models\User();
$productModel = new MonolithicArchitecture\Models\Product();
$orderModel = new MonolithicArchitecture\Models\Order();
$paymentModel = new MonolithicArchitecture\Models\Payment();
echo "Models created.\n\n";

// STEP 6: Set up the utilities
echo "STEP 6: Setting up the utilities\n";
// In a monolithic application, utilities provide common functionality
$logger = new MonolithicArchitecture\Utils\Logger();
$validator = new MonolithicArchitecture\Utils\Validator();
$emailSender = new MonolithicArchitecture\Utils\EmailSender();
echo "Utilities set up.\n\n";

// SCENARIO 1: User authentication
echo "SCENARIO 1: User authentication\n";
echo "----------------------------------------\n";

// Simulate a login request
$loginRequest = [
    'email' => 'john.doe@example.com',
    'password' => 'password123'
];

try {
    // In a monolithic application, the controller directly calls the service
    $response = $userController->login($loginRequest);

    if ($response['status'] === 'success') {
        $user = isset($response['data']['user']) ? $response['data']['user'] : [];
        $token = isset($response['data']['token']) ? $response['data']['token'] : '';

        echo "User authenticated successfully:\n";
        echo "- User ID: " . (isset($user['id']) ? $user['id'] : 'N/A') . "\n";
        echo "- Email: " . (isset($user['email']) ? $user['email'] : 'N/A') . "\n";
        echo "- Role: " . (isset($user['role']) ? $user['role'] : 'N/A') . "\n";
        echo "- Token: $token\n";

        // Set user headers for subsequent requests
        $userHeaders = [
            'X-User-ID' => isset($user['id']) ? $user['id'] : 0,
            'X-User-Role' => isset($user['role']) ? $user['role'] : 'user',
            'Authorization' => "Bearer $token"
        ];
    } else {
        echo "Authentication failed: {$response['message']}\n";
        exit;
    }
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
    exit;
}
echo "\n";

// SCENARIO 2: Browsing products
echo "SCENARIO 2: Browsing products\n";
echo "----------------------------------------\n";

try {
    // In a monolithic application, the controller directly calls the service
    $response = $productController->getProducts($userHeaders);

    if ($response['status'] === 'success') {
        $products = isset($response['data']) ? $response['data'] : [];

        echo "Available products:\n";
        if (is_array($products) && !empty($products)) {
            foreach ($products as $product) {
                $name = isset($product['name']) ? $product['name'] : 'Unknown';
                $price = isset($product['price']) ? $product['price'] : 0;
                $description = isset($product['description']) ? $product['description'] : 'No description';
                echo "- {$name} (\${$price}) - {$description}\n";
            }

            // Select a product for the order
            $selectedProduct = $products[0];
            $productName = isset($selectedProduct['name']) ? $selectedProduct['name'] : 'Unknown';
            $productPrice = isset($selectedProduct['price']) ? $selectedProduct['price'] : 0;
            echo "\nSelected product: {$productName} (\${$productPrice})\n";
        } else {
            echo "- No products available\n";

            // Create a default product if no products are available
            $selectedProduct = [
                'id' => 0,
                'name' => 'Default Product',
                'price' => 0,
                'description' => 'Default Description'
            ];
            echo "\nSelected product: Default Product ($0)\n";
        }
    } else {
        echo "Failed to retrieve products: {$response['message']}\n";
        exit;
    }
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
    exit;
}
echo "\n";

// SCENARIO 3: Creating an order
echo "SCENARIO 3: Creating an order\n";
echo "----------------------------------------\n";

// Prepare order data
$orderData = [
    'user_id' => isset($user['id']) ? $user['id'] : 0,
    'items' => [
        [
            'product_id' => isset($selectedProduct['id']) ? $selectedProduct['id'] : 0,
            'quantity' => 1,
            'price' => isset($selectedProduct['price']) ? $selectedProduct['price'] : 0
        ]
    ]
];

try {
    // In a monolithic application, the controller directly calls the service
    $response = $orderController->createOrder($orderData, $userHeaders);

    if ($response['status'] === 'success') {
        $order = isset($response['data']['order']) ? $response['data']['order'] : [];
        $items = isset($response['data']['items']) ? $response['data']['items'] : [];

        echo "Order created successfully:\n";
        echo "- Order ID: " . (isset($order['id']) ? $order['id'] : 'N/A') . "\n";
        echo "- Total: $" . (isset($order['total']) ? $order['total'] : '0.00') . "\n";
        echo "- Status: " . (isset($order['status']) ? $order['status'] : 'N/A') . "\n";
        echo "- Items:\n";
        if (is_array($items)) {
            foreach ($items as $item) {
                $productId = isset($item['product_id']) ? $item['product_id'] : 'N/A';
                $quantity = isset($item['quantity']) ? $item['quantity'] : 0;
                $price = isset($item['price']) ? $item['price'] : 0;
                echo "  - Product ID: {$productId}, Quantity: {$quantity}, Price: \${$price}\n";
            }
        }
    } else {
        echo "Failed to create order: {$response['message']}\n";
        exit;
    }
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
    exit;
}
echo "\n";

// SCENARIO 4: Processing payment
echo "SCENARIO 4: Processing payment\n";
echo "----------------------------------------\n";

// Get user's payment methods
try {
    $userId = isset($user['id']) ? $user['id'] : 0;
    // In a monolithic application, the controller directly calls the service
    $response = $paymentController->getPaymentMethods($userId, $userHeaders);

    if ($response['status'] === 'success') {
        $paymentMethods = isset($response['data']) ? $response['data'] : [];

        echo "Available payment methods:\n";
        if (is_array($paymentMethods) && !empty($paymentMethods)) {
            foreach ($paymentMethods as $method) {
                $isDefault = isset($method['is_default']) ? $method['is_default'] : false;
                $type = isset($method['type']) ? $method['type'] : 'Unknown';
                // Get the human-readable name for the payment method type
                $typeName = '';
                if ($type === 'credit_card') {
                    $typeName = 'Credit Card';
                } elseif ($type === 'debit_card') {
                    $typeName = 'Debit Card';
                } elseif ($type === 'paypal') {
                    $typeName = 'PayPal';
                } elseif ($type === 'bank_transfer') {
                    $typeName = 'Bank Transfer';
                } else {
                    $typeName = $type;
                }
                $defaultText = $isDefault ? ' (default)' : '';
                echo "- {$typeName}{$defaultText}\n";
            }
        } else {
            echo "- No payment methods available\n";
        }

        // Select the default payment method
        $selectedPaymentMethod = null;
        if (is_array($paymentMethods) && !empty($paymentMethods)) {
            foreach ($paymentMethods as $method) {
                if (isset($method['is_default']) && $method['is_default']) {
                    $selectedPaymentMethod = $method;
                    break;
                }
            }

            if (!$selectedPaymentMethod && isset($paymentMethods[0])) {
                $selectedPaymentMethod = $paymentMethods[0];
            }
        }

        // Create a default payment method if none found
        if (!$selectedPaymentMethod) {
            $selectedPaymentMethod = [
                'id' => 0,
                'type' => 'credit_card',
                'is_default' => true
            ];
        }

        // Get the human-readable name for the selected payment method type
        $selectedType = isset($selectedPaymentMethod['type']) ? $selectedPaymentMethod['type'] : 'Unknown';
        $selectedTypeName = '';
        if ($selectedType === 'credit_card') {
            $selectedTypeName = 'Credit Card';
        } elseif ($selectedType === 'debit_card') {
            $selectedTypeName = 'Debit Card';
        } elseif ($selectedType === 'paypal') {
            $selectedTypeName = 'PayPal';
        } elseif ($selectedType === 'bank_transfer') {
            $selectedTypeName = 'Bank Transfer';
        } else {
            $selectedTypeName = $selectedType;
        }

        echo "\nSelected payment method: {$selectedTypeName}\n";
    } else {
        echo "Failed to retrieve payment methods: {$response['message']}\n";
        exit;
    }
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
    exit;
}

// Process payment
$paymentData = [
    'user_id' => isset($user['id']) ? $user['id'] : 0,
    'order_id' => isset($order['id']) ? $order['id'] : 0,
    'payment_method_id' => isset($selectedPaymentMethod['id']) ? $selectedPaymentMethod['id'] : 0,
    'amount' => isset($order['total']) ? $order['total'] : 0,
    'currency' => 'USD',
    'description' => "Payment for Order #" . (isset($order['id']) ? $order['id'] : 0)
];

try {
    // In a monolithic application, the controller directly calls the service
    $response = $paymentController->processPayment($paymentData, $userHeaders);

    if ($response['status'] === 'success') {
        $transaction = isset($response['data']) ? $response['data'] : [];

        echo "Payment processed successfully:\n";
        echo "- Transaction ID: " . (isset($transaction['id']) ? $transaction['id'] : 'N/A') . "\n";
        echo "- Amount: $" . (isset($transaction['amount']) ? number_format($transaction['amount'], 2) : '0.00') . " " . 
            (isset($transaction['currency']) ? $transaction['currency'] : 'USD') . "\n";
        echo "- Status: " . (isset($transaction['status']) ? $transaction['status'] : 'N/A') . "\n";
        echo "- Reference: " . (isset($transaction['reference']) ? $transaction['reference'] : 'N/A') . "\n";
    } else {
        echo "Failed to process payment: {$response['message']}\n";
        exit;
    }
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
    exit;
}
echo "\n";

// SCENARIO 5: Checking order status
echo "SCENARIO 5: Checking order status\n";
echo "----------------------------------------\n";

try {
    $orderId = isset($order['id']) ? $order['id'] : 0;
    // In a monolithic application, the controller directly calls the service
    $response = $orderController->getOrder($orderId, $userHeaders);

    if ($response['status'] === 'success') {
        $updatedOrder = isset($response['data']['order']) ? $response['data']['order'] : [];
        $items = isset($response['data']['items']) ? $response['data']['items'] : [];

        echo "Order status:\n";
        echo "- Order ID: " . (isset($updatedOrder['id']) ? $updatedOrder['id'] : 'N/A') . "\n";
        echo "- Total: $" . (isset($updatedOrder['total']) ? $updatedOrder['total'] : '0.00') . "\n";
        echo "- Status: " . (isset($updatedOrder['status']) ? $updatedOrder['status'] : 'N/A') . "\n";
        echo "- Created at: " . (isset($updatedOrder['created_at']) ? $updatedOrder['created_at'] : 'N/A') . "\n";
        echo "- Updated at: " . (isset($updatedOrder['updated_at']) ? $updatedOrder['updated_at'] : 'N/A') . "\n";
    } else {
        echo "Failed to retrieve order: {$response['message']}\n";
        exit;
    }
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
    exit;
}
echo "\n";

// SCENARIO 6: Error handling in a monolithic application
echo "SCENARIO 6: Error handling in a monolithic application\n";
echo "----------------------------------------\n";

echo "In a monolithic application, error handling is typically centralized.\n";
echo "Errors are caught and handled within the application, often with a global error handler.\n";
echo "This can make debugging easier since all components are in the same codebase.\n";
echo "However, a critical error can potentially bring down the entire application.\n\n";

// SCENARIO 7: Scaling a monolithic application
echo "SCENARIO 7: Scaling a monolithic application\n";
echo "----------------------------------------\n";

echo "Scaling a monolithic application typically involves:\n";
echo "1. Vertical scaling (adding more resources to the same server)\n";
echo "2. Horizontal scaling (adding more instances of the entire application)\n";
echo "3. Caching strategies to reduce database load\n";
echo "4. Database optimization and sharding\n\n";

echo "This example demonstrates the key aspects of the Monolithic pattern:\n";
echo "1. All components are part of a single, unified codebase\n";
echo "2. Components communicate via direct method calls\n";
echo "3. The application shares a single database\n";
echo "4. Deployment is done as a single unit\n";
echo "5. Scaling is typically done by replicating the entire application\n";
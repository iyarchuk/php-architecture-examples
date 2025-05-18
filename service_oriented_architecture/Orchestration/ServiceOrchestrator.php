<?php

namespace ServiceOrientedArchitecture\Orchestration;

use ServiceOrientedArchitecture\Client\ServiceClient;

/**
 * ServiceOrchestrator coordinates the interaction between multiple services.
 * In a Service-Oriented Architecture, orchestration is used to coordinate
 * complex business processes that span multiple services.
 */
class ServiceOrchestrator
{
    /**
     * @var ServiceClient The service client
     */
    private $client;
    
    /**
     * Constructor
     *
     * @param ServiceClient $client The service client
     */
    public function __construct(ServiceClient $client)
    {
        $this->client = $client;
    }
    
    /**
     * Create a user with products
     *
     * This is an example of orchestration where we create a user and then
     * associate products with that user.
     *
     * @param array $userData The user data
     * @param array $productIds The IDs of products to associate with the user
     * @return array The result of the operation
     */
    public function createUserWithProducts(array $userData, array $productIds): array
    {
        try {
            // Step 1: Create the user
            $createUserResult = $this->client->call('user-service', 'createUser', $userData);
            
            if (!$createUserResult['success']) {
                return [
                    'success' => false,
                    'message' => 'Failed to create user: ' . ($createUserResult['message'] ?? 'Unknown error'),
                    'step' => 'create_user'
                ];
            }
            
            $userId = $createUserResult['data']['id'];
            
            // Step 2: Get product details for each product ID
            $products = [];
            foreach ($productIds as $productId) {
                $getProductResult = $this->client->call('product-service', 'getProduct', ['productId' => $productId]);
                
                if (!$getProductResult['success']) {
                    // Rollback: Delete the user
                    $this->client->call('user-service', 'deleteUser', ['userId' => $userId]);
                    
                    return [
                        'success' => false,
                        'message' => 'Failed to get product: ' . ($getProductResult['message'] ?? 'Unknown error'),
                        'step' => 'get_product',
                        'productId' => $productId
                    ];
                }
                
                $products[] = $getProductResult['data'];
            }
            
            // Step 3: In a real application, we would associate the products with the user
            // For this example, we'll just return the user and products
            
            return [
                'success' => true,
                'message' => 'User created with products successfully',
                'data' => [
                    'user' => $createUserResult['data'],
                    'products' => $products
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Orchestration failed: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Process an order
     *
     * This is an example of orchestration where we process an order by
     * checking product availability, creating the order, and updating inventory.
     *
     * @param string $userId The ID of the user placing the order
     * @param array $items The items in the order (product ID and quantity)
     * @return array The result of the operation
     */
    public function processOrder(string $userId, array $items): array
    {
        try {
            // Step 1: Check if the user exists
            $getUserResult = $this->client->call('user-service', 'getUser', ['userId' => $userId]);
            
            if (!$getUserResult['success']) {
                return [
                    'success' => false,
                    'message' => 'Failed to get user: ' . ($getUserResult['message'] ?? 'Unknown error'),
                    'step' => 'get_user'
                ];
            }
            
            // Step 2: Check product availability for each item
            $orderItems = [];
            $totalAmount = 0;
            
            foreach ($items as $item) {
                $productId = $item['productId'] ?? '';
                $quantity = $item['quantity'] ?? 0;
                
                if (empty($productId) || $quantity <= 0) {
                    return [
                        'success' => false,
                        'message' => 'Invalid item: Product ID and quantity are required',
                        'step' => 'validate_item'
                    ];
                }
                
                // Get product details
                $getProductResult = $this->client->call('product-service', 'getProduct', ['productId' => $productId]);
                
                if (!$getProductResult['success']) {
                    return [
                        'success' => false,
                        'message' => 'Failed to get product: ' . ($getProductResult['message'] ?? 'Unknown error'),
                        'step' => 'get_product',
                        'productId' => $productId
                    ];
                }
                
                $product = $getProductResult['data'];
                
                // In a real application, we would check inventory here
                // For this example, we'll assume all products are available
                
                // Calculate item total
                $itemTotal = $product['price'] * $quantity;
                $totalAmount += $itemTotal;
                
                $orderItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'itemTotal' => $itemTotal
                ];
            }
            
            // Step 3: In a real application, we would create the order in an order service
            // For this example, we'll just return the order details
            
            $order = [
                'id' => uniqid('order-'),
                'userId' => $userId,
                'items' => $orderItems,
                'totalAmount' => $totalAmount,
                'status' => 'created',
                'createdAt' => date('Y-m-d H:i:s')
            ];
            
            return [
                'success' => true,
                'message' => 'Order processed successfully',
                'data' => [
                    'order' => $order
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Orchestration failed: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Search products and get user details
     *
     * This is an example of orchestration where we search for products
     * and get details of the user who created them.
     *
     * @param string $query The search query
     * @param string $userId The ID of the user
     * @return array The result of the operation
     */
    public function searchProductsAndGetUserDetails(string $query, string $userId): array
    {
        try {
            // Step 1: Search for products
            $searchProductsResult = $this->client->call('product-service', 'searchProducts', [
                'query' => $query,
                'options' => [
                    'limit' => 10,
                    'sort_by' => 'name',
                    'sort_direction' => 'asc'
                ]
            ]);
            
            if (!$searchProductsResult['success']) {
                return [
                    'success' => false,
                    'message' => 'Failed to search products: ' . ($searchProductsResult['message'] ?? 'Unknown error'),
                    'step' => 'search_products'
                ];
            }
            
            // Step 2: Get user details
            $getUserResult = $this->client->call('user-service', 'getUser', ['userId' => $userId]);
            
            if (!$getUserResult['success']) {
                return [
                    'success' => false,
                    'message' => 'Failed to get user: ' . ($getUserResult['message'] ?? 'Unknown error'),
                    'step' => 'get_user'
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Products and user details retrieved successfully',
                'data' => [
                    'products' => $searchProductsResult['data'],
                    'user' => $getUserResult['data']
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Orchestration failed: ' . $e->getMessage()
            ];
        }
    }
}
<?php

namespace MicroservicesArchitecture\Services\ProductService;

use MicroservicesArchitecture\Services\BaseService;

/**
 * ProductService
 * 
 * This class implements the product catalog microservice.
 * It provides endpoints for managing products, categories, and inventory.
 */
class ProductService extends BaseService
{
    /**
     * @var array The products database (in-memory for this example)
     */
    private array $products = [];
    
    /**
     * @var array The categories database (in-memory for this example)
     */
    private array $categories = [];
    
    /**
     * @var array The inventory database (in-memory for this example)
     */
    private array $inventory = [];
    
    /**
     * Constructor
     * 
     * @param string $url The URL of the service
     */
    public function __construct(string $url = 'http://product-service.example.com')
    {
        parent::__construct('product-service', $url);
        
        // Initialize with some sample data
        $this->initializeSampleData();
    }
    
    /**
     * Initialize the endpoints supported by the service
     * 
     * @return void
     */
    protected function initializeEndpoints(): void
    {
        $this->endpoints = [
            '/products' => [
                'methods' => ['GET', 'POST'],
                'handler' => [$this, 'handleProducts']
            ],
            '/products/{id}' => [
                'methods' => ['GET', 'PUT', 'DELETE'],
                'handler' => [$this, 'handleProduct']
            ],
            '/categories' => [
                'methods' => ['GET', 'POST'],
                'handler' => [$this, 'handleCategories']
            ],
            '/categories/{id}' => [
                'methods' => ['GET', 'PUT', 'DELETE'],
                'handler' => [$this, 'handleCategory']
            ],
            '/inventory' => [
                'methods' => ['GET'],
                'handler' => [$this, 'handleInventory']
            ],
            '/inventory/{productId}' => [
                'methods' => ['GET', 'PUT'],
                'handler' => [$this, 'handleInventoryItem']
            ],
            '/health' => [
                'methods' => ['GET'],
                'handler' => [$this, 'handleHealth']
            ]
        ];
        
        // Define topics to subscribe to
        $this->subscribeTopics = [
            'order-created' => [$this, 'handleOrderCreatedEvent'],
            'order-cancelled' => [$this, 'handleOrderCancelledEvent']
        ];
    }
    
    /**
     * Initialize sample data
     * 
     * @return void
     */
    private function initializeSampleData(): void
    {
        // Sample categories
        $this->categories = [
            1 => ['id' => 1, 'name' => 'Electronics', 'description' => 'Electronic devices and accessories'],
            2 => ['id' => 2, 'name' => 'Clothing', 'description' => 'Apparel and fashion items'],
            3 => ['id' => 3, 'name' => 'Books', 'description' => 'Books and publications']
        ];
        
        // Sample products
        $this->products = [
            1 => [
                'id' => 1,
                'name' => 'Smartphone',
                'description' => 'High-end smartphone with advanced features',
                'price' => 699.99,
                'category_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            2 => [
                'id' => 2,
                'name' => 'Laptop',
                'description' => 'Powerful laptop for work and gaming',
                'price' => 1299.99,
                'category_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            3 => [
                'id' => 3,
                'name' => 'T-Shirt',
                'description' => 'Comfortable cotton t-shirt',
                'price' => 19.99,
                'category_id' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            4 => [
                'id' => 4,
                'name' => 'Programming Book',
                'description' => 'Learn programming with this comprehensive guide',
                'price' => 49.99,
                'category_id' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        // Sample inventory
        $this->inventory = [
            1 => ['product_id' => 1, 'quantity' => 100, 'updated_at' => date('Y-m-d H:i:s')],
            2 => ['product_id' => 2, 'quantity' => 50, 'updated_at' => date('Y-m-d H:i:s')],
            3 => ['product_id' => 3, 'quantity' => 200, 'updated_at' => date('Y-m-d H:i:s')],
            4 => ['product_id' => 4, 'quantity' => 75, 'updated_at' => date('Y-m-d H:i:s')]
        ];
    }
    
    /**
     * Handle requests to the /products endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleProducts(string $method, array $data, array $headers): array
    {
        switch ($method) {
            case 'GET':
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => array_values($this->products)
                ];
            
            case 'POST':
                // Validate required fields
                if (empty($data['name']) || empty($data['price']) || empty($data['category_id'])) {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Missing required fields'
                    ];
                }
                
                // Check if category exists
                if (!isset($this->categories[$data['category_id']])) {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Invalid category ID'
                    ];
                }
                
                // Create new product
                $id = count($this->products) + 1;
                $product = [
                    'id' => $id,
                    'name' => $data['name'],
                    'description' => $data['description'] ?? '',
                    'price' => (float) $data['price'],
                    'category_id' => (int) $data['category_id'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                $this->products[$id] = $product;
                
                // Initialize inventory
                $this->inventory[$id] = [
                    'product_id' => $id,
                    'quantity' => $data['quantity'] ?? 0,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                return [
                    'status' => 'success',
                    'code' => 201,
                    'data' => $product
                ];
            
            default:
                return [
                    'status' => 'error',
                    'code' => 405,
                    'message' => 'Method not allowed'
                ];
        }
    }
    
    /**
     * Handle requests to the /products/{id} endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleProduct(string $method, array $data, array $headers): array
    {
        // Extract product ID from the endpoint
        $id = isset($data['id']) ? (int) $data['id'] : 0;
        
        // Check if product exists
        if (!isset($this->products[$id])) {
            return [
                'status' => 'error',
                'code' => 404,
                'message' => 'Product not found'
            ];
        }
        
        switch ($method) {
            case 'GET':
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $this->products[$id]
                ];
            
            case 'PUT':
                // Update product
                if (isset($data['name'])) {
                    $this->products[$id]['name'] = $data['name'];
                }
                
                if (isset($data['description'])) {
                    $this->products[$id]['description'] = $data['description'];
                }
                
                if (isset($data['price'])) {
                    $this->products[$id]['price'] = (float) $data['price'];
                }
                
                if (isset($data['category_id'])) {
                    // Check if category exists
                    if (!isset($this->categories[$data['category_id']])) {
                        return [
                            'status' => 'error',
                            'code' => 400,
                            'message' => 'Invalid category ID'
                        ];
                    }
                    
                    $this->products[$id]['category_id'] = (int) $data['category_id'];
                }
                
                $this->products[$id]['updated_at'] = date('Y-m-d H:i:s');
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $this->products[$id]
                ];
            
            case 'DELETE':
                // Delete product
                $product = $this->products[$id];
                unset($this->products[$id]);
                
                // Delete inventory
                unset($this->inventory[$id]);
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $product
                ];
            
            default:
                return [
                    'status' => 'error',
                    'code' => 405,
                    'message' => 'Method not allowed'
                ];
        }
    }
    
    /**
     * Handle requests to the /categories endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleCategories(string $method, array $data, array $headers): array
    {
        switch ($method) {
            case 'GET':
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => array_values($this->categories)
                ];
            
            case 'POST':
                // Validate required fields
                if (empty($data['name'])) {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Missing required fields'
                    ];
                }
                
                // Create new category
                $id = count($this->categories) + 1;
                $category = [
                    'id' => $id,
                    'name' => $data['name'],
                    'description' => $data['description'] ?? ''
                ];
                
                $this->categories[$id] = $category;
                
                return [
                    'status' => 'success',
                    'code' => 201,
                    'data' => $category
                ];
            
            default:
                return [
                    'status' => 'error',
                    'code' => 405,
                    'message' => 'Method not allowed'
                ];
        }
    }
    
    /**
     * Handle requests to the /categories/{id} endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleCategory(string $method, array $data, array $headers): array
    {
        // Extract category ID from the endpoint
        $id = isset($data['id']) ? (int) $data['id'] : 0;
        
        // Check if category exists
        if (!isset($this->categories[$id])) {
            return [
                'status' => 'error',
                'code' => 404,
                'message' => 'Category not found'
            ];
        }
        
        switch ($method) {
            case 'GET':
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $this->categories[$id]
                ];
            
            case 'PUT':
                // Update category
                if (isset($data['name'])) {
                    $this->categories[$id]['name'] = $data['name'];
                }
                
                if (isset($data['description'])) {
                    $this->categories[$id]['description'] = $data['description'];
                }
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $this->categories[$id]
                ];
            
            case 'DELETE':
                // Check if category is in use
                foreach ($this->products as $product) {
                    if ($product['category_id'] === $id) {
                        return [
                            'status' => 'error',
                            'code' => 400,
                            'message' => 'Cannot delete category that is in use'
                        ];
                    }
                }
                
                // Delete category
                $category = $this->categories[$id];
                unset($this->categories[$id]);
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $category
                ];
            
            default:
                return [
                    'status' => 'error',
                    'code' => 405,
                    'message' => 'Method not allowed'
                ];
        }
    }
    
    /**
     * Handle requests to the /inventory endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleInventory(string $method, array $data, array $headers): array
    {
        switch ($method) {
            case 'GET':
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => array_values($this->inventory)
                ];
            
            default:
                return [
                    'status' => 'error',
                    'code' => 405,
                    'message' => 'Method not allowed'
                ];
        }
    }
    
    /**
     * Handle requests to the /inventory/{productId} endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleInventoryItem(string $method, array $data, array $headers): array
    {
        // Extract product ID from the endpoint
        $productId = isset($data['productId']) ? (int) $data['productId'] : 0;
        
        // Check if product exists
        if (!isset($this->products[$productId])) {
            return [
                'status' => 'error',
                'code' => 404,
                'message' => 'Product not found'
            ];
        }
        
        // Check if inventory exists
        if (!isset($this->inventory[$productId])) {
            // Initialize inventory
            $this->inventory[$productId] = [
                'product_id' => $productId,
                'quantity' => 0,
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        
        switch ($method) {
            case 'GET':
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $this->inventory[$productId]
                ];
            
            case 'PUT':
                // Update inventory
                if (isset($data['quantity'])) {
                    $this->inventory[$productId]['quantity'] = (int) $data['quantity'];
                    $this->inventory[$productId]['updated_at'] = date('Y-m-d H:i:s');
                }
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $this->inventory[$productId]
                ];
            
            default:
                return [
                    'status' => 'error',
                    'code' => 405,
                    'message' => 'Method not allowed'
                ];
        }
    }
    
    /**
     * Handle requests to the /health endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleHealth(string $method, array $data, array $headers): array
    {
        return [
            'status' => 'success',
            'code' => 200,
            'data' => [
                'service' => $this->name,
                'status' => 'healthy',
                'timestamp' => date('Y-m-d H:i:s'),
                'version' => '1.0.0'
            ]
        ];
    }
    
    /**
     * Handle the order-created event
     * 
     * @param array $event The event data
     * @return void
     */
    public function handleOrderCreatedEvent(array $event): void
    {
        // Update inventory based on order items
        if (isset($event['message']['items'])) {
            foreach ($event['message']['items'] as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];
                
                if (isset($this->inventory[$productId])) {
                    $this->inventory[$productId]['quantity'] -= $quantity;
                    $this->inventory[$productId]['updated_at'] = date('Y-m-d H:i:s');
                }
            }
        }
    }
    
    /**
     * Handle the order-cancelled event
     * 
     * @param array $event The event data
     * @return void
     */
    public function handleOrderCancelledEvent(array $event): void
    {
        // Restore inventory based on order items
        if (isset($event['message']['items'])) {
            foreach ($event['message']['items'] as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];
                
                if (isset($this->inventory[$productId])) {
                    $this->inventory[$productId]['quantity'] += $quantity;
                    $this->inventory[$productId]['updated_at'] = date('Y-m-d H:i:s');
                }
            }
        }
    }
}
<?php

namespace MicroservicesArchitecture\Services\OrderService;

use MicroservicesArchitecture\Services\BaseService;
use MicroservicesArchitecture\MessageBroker\Broker;

/**
 * OrderService
 * 
 * This class implements the order processing microservice.
 * It provides endpoints for managing orders and order items.
 */
class OrderService extends BaseService
{
    /**
     * @var array The orders database (in-memory for this example)
     */
    private array $orders = [];
    
    /**
     * @var array The order items database (in-memory for this example)
     */
    private array $orderItems = [];
    
    /**
     * @var array The order statuses
     */
    private array $orderStatuses = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled'
    ];
    
    /**
     * Constructor
     * 
     * @param string $url The URL of the service
     */
    public function __construct(string $url = 'http://order-service.example.com')
    {
        parent::__construct('order-service', $url);
        
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
            '/orders' => [
                'methods' => ['GET', 'POST'],
                'handler' => [$this, 'handleOrders']
            ],
            '/orders/{id}' => [
                'methods' => ['GET', 'PUT', 'DELETE'],
                'handler' => [$this, 'handleOrder']
            ],
            '/orders/{id}/items' => [
                'methods' => ['GET', 'POST'],
                'handler' => [$this, 'handleOrderItems']
            ],
            '/orders/{id}/items/{itemId}' => [
                'methods' => ['GET', 'PUT', 'DELETE'],
                'handler' => [$this, 'handleOrderItem']
            ],
            '/orders/{id}/status' => [
                'methods' => ['PUT'],
                'handler' => [$this, 'handleOrderStatus']
            ],
            '/statuses' => [
                'methods' => ['GET'],
                'handler' => [$this, 'handleStatuses']
            ],
            '/health' => [
                'methods' => ['GET'],
                'handler' => [$this, 'handleHealth']
            ]
        ];
        
        // Define topics to subscribe to
        $this->subscribeTopics = [
            'payment-processed' => [$this, 'handlePaymentProcessedEvent'],
            'payment-failed' => [$this, 'handlePaymentFailedEvent']
        ];
    }
    
    /**
     * Initialize sample data
     * 
     * @return void
     */
    private function initializeSampleData(): void
    {
        // Sample orders
        $this->orders = [
            1 => [
                'id' => 1,
                'user_id' => 1,
                'status' => 'delivered',
                'total' => 699.99,
                'created_at' => date('Y-m-d H:i:s', strtotime('-7 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
            ],
            2 => [
                'id' => 2,
                'user_id' => 2,
                'status' => 'processing',
                'total' => 1299.99,
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ],
            3 => [
                'id' => 3,
                'user_id' => 1,
                'status' => 'pending',
                'total' => 69.98,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        // Sample order items
        $this->orderItems = [
            1 => [
                'id' => 1,
                'order_id' => 1,
                'product_id' => 1,
                'quantity' => 1,
                'price' => 699.99,
                'total' => 699.99
            ],
            2 => [
                'id' => 2,
                'order_id' => 2,
                'product_id' => 2,
                'quantity' => 1,
                'price' => 1299.99,
                'total' => 1299.99
            ],
            3 => [
                'id' => 3,
                'order_id' => 3,
                'product_id' => 3,
                'quantity' => 2,
                'price' => 19.99,
                'total' => 39.98
            ],
            4 => [
                'id' => 4,
                'order_id' => 3,
                'product_id' => 4,
                'quantity' => 1,
                'price' => 30.00,
                'total' => 30.00
            ]
        ];
    }
    
    /**
     * Handle requests to the /orders endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleOrders(string $method, array $data, array $headers): array
    {
        switch ($method) {
            case 'GET':
                // Filter by user_id if provided
                $userId = $data['user_id'] ?? null;
                
                if ($userId) {
                    $filteredOrders = array_filter($this->orders, function ($order) use ($userId) {
                        return $order['user_id'] == $userId;
                    });
                    
                    return [
                        'status' => 'success',
                        'code' => 200,
                        'data' => array_values($filteredOrders)
                    ];
                }
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => array_values($this->orders)
                ];
            
            case 'POST':
                // Validate required fields
                if (empty($data['user_id']) || empty($data['items'])) {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Missing required fields'
                    ];
                }
                
                // Create new order
                $id = count($this->orders) + 1;
                $total = 0;
                
                // Calculate total from items
                foreach ($data['items'] as $item) {
                    if (empty($item['product_id']) || empty($item['quantity']) || empty($item['price'])) {
                        return [
                            'status' => 'error',
                            'code' => 400,
                            'message' => 'Invalid item data'
                        ];
                    }
                    
                    $total += $item['quantity'] * $item['price'];
                }
                
                $order = [
                    'id' => $id,
                    'user_id' => (int) $data['user_id'],
                    'status' => 'pending',
                    'total' => $total,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                $this->orders[$id] = $order;
                
                // Create order items
                $itemId = count($this->orderItems) + 1;
                $items = [];
                
                foreach ($data['items'] as $item) {
                    $orderItem = [
                        'id' => $itemId,
                        'order_id' => $id,
                        'product_id' => (int) $item['product_id'],
                        'quantity' => (int) $item['quantity'],
                        'price' => (float) $item['price'],
                        'total' => (float) $item['quantity'] * (float) $item['price']
                    ];
                    
                    $this->orderItems[$itemId] = $orderItem;
                    $items[] = $orderItem;
                    $itemId++;
                }
                
                // Publish order-created event
                if (isset($data['broker']) && $data['broker'] instanceof Broker) {
                    $this->publishEvent($data['broker'], 'order-created', [
                        'order_id' => $id,
                        'user_id' => $order['user_id'],
                        'total' => $order['total'],
                        'items' => $items
                    ]);
                }
                
                return [
                    'status' => 'success',
                    'code' => 201,
                    'data' => [
                        'order' => $order,
                        'items' => $items
                    ]
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
     * Handle requests to the /orders/{id} endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleOrder(string $method, array $data, array $headers): array
    {
        // Extract order ID from the endpoint
        $id = isset($data['id']) ? (int) $data['id'] : 0;
        
        // Check if order exists
        if (!isset($this->orders[$id])) {
            return [
                'status' => 'error',
                'code' => 404,
                'message' => 'Order not found'
            ];
        }
        
        switch ($method) {
            case 'GET':
                // Get order items
                $items = array_filter($this->orderItems, function ($item) use ($id) {
                    return $item['order_id'] == $id;
                });
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => [
                        'order' => $this->orders[$id],
                        'items' => array_values($items)
                    ]
                ];
            
            case 'PUT':
                // Only allow updating certain fields
                if (isset($data['status'])) {
                    // Validate status
                    if (!isset($this->orderStatuses[$data['status']])) {
                        return [
                            'status' => 'error',
                            'code' => 400,
                            'message' => 'Invalid status'
                        ];
                    }
                    
                    $oldStatus = $this->orders[$id]['status'];
                    $newStatus = $data['status'];
                    
                    // Update status
                    $this->orders[$id]['status'] = $newStatus;
                    $this->orders[$id]['updated_at'] = date('Y-m-d H:i:s');
                    
                    // If status changed to cancelled, publish event
                    if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                        // Get order items
                        $items = array_filter($this->orderItems, function ($item) use ($id) {
                            return $item['order_id'] == $id;
                        });
                        
                        // Publish order-cancelled event
                        if (isset($data['broker']) && $data['broker'] instanceof Broker) {
                            $this->publishEvent($data['broker'], 'order-cancelled', [
                                'order_id' => $id,
                                'user_id' => $this->orders[$id]['user_id'],
                                'total' => $this->orders[$id]['total'],
                                'items' => array_values($items)
                            ]);
                        }
                    }
                }
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $this->orders[$id]
                ];
            
            case 'DELETE':
                // Check if order can be deleted (only pending orders)
                if ($this->orders[$id]['status'] !== 'pending') {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Only pending orders can be deleted'
                    ];
                }
                
                // Get order items
                $items = array_filter($this->orderItems, function ($item) use ($id) {
                    return $item['order_id'] == $id;
                });
                
                // Delete order items
                foreach ($items as $item) {
                    unset($this->orderItems[$item['id']]);
                }
                
                // Delete order
                $order = $this->orders[$id];
                unset($this->orders[$id]);
                
                // Publish order-cancelled event
                if (isset($data['broker']) && $data['broker'] instanceof Broker) {
                    $this->publishEvent($data['broker'], 'order-cancelled', [
                        'order_id' => $id,
                        'user_id' => $order['user_id'],
                        'total' => $order['total'],
                        'items' => array_values($items)
                    ]);
                }
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $order
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
     * Handle requests to the /orders/{id}/items endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleOrderItems(string $method, array $data, array $headers): array
    {
        // Extract order ID from the endpoint
        $id = isset($data['id']) ? (int) $data['id'] : 0;
        
        // Check if order exists
        if (!isset($this->orders[$id])) {
            return [
                'status' => 'error',
                'code' => 404,
                'message' => 'Order not found'
            ];
        }
        
        switch ($method) {
            case 'GET':
                // Get order items
                $items = array_filter($this->orderItems, function ($item) use ($id) {
                    return $item['order_id'] == $id;
                });
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => array_values($items)
                ];
            
            case 'POST':
                // Check if order can be modified (only pending orders)
                if ($this->orders[$id]['status'] !== 'pending') {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Only pending orders can be modified'
                    ];
                }
                
                // Validate required fields
                if (empty($data['product_id']) || empty($data['quantity']) || empty($data['price'])) {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Missing required fields'
                    ];
                }
                
                // Create new order item
                $itemId = count($this->orderItems) + 1;
                $orderItem = [
                    'id' => $itemId,
                    'order_id' => $id,
                    'product_id' => (int) $data['product_id'],
                    'quantity' => (int) $data['quantity'],
                    'price' => (float) $data['price'],
                    'total' => (float) $data['quantity'] * (float) $data['price']
                ];
                
                $this->orderItems[$itemId] = $orderItem;
                
                // Update order total
                $this->orders[$id]['total'] += $orderItem['total'];
                $this->orders[$id]['updated_at'] = date('Y-m-d H:i:s');
                
                return [
                    'status' => 'success',
                    'code' => 201,
                    'data' => $orderItem
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
     * Handle requests to the /orders/{id}/items/{itemId} endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleOrderItem(string $method, array $data, array $headers): array
    {
        // Extract order ID and item ID from the endpoint
        $id = isset($data['id']) ? (int) $data['id'] : 0;
        $itemId = isset($data['itemId']) ? (int) $data['itemId'] : 0;
        
        // Check if order exists
        if (!isset($this->orders[$id])) {
            return [
                'status' => 'error',
                'code' => 404,
                'message' => 'Order not found'
            ];
        }
        
        // Check if order item exists
        if (!isset($this->orderItems[$itemId]) || $this->orderItems[$itemId]['order_id'] != $id) {
            return [
                'status' => 'error',
                'code' => 404,
                'message' => 'Order item not found'
            ];
        }
        
        switch ($method) {
            case 'GET':
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $this->orderItems[$itemId]
                ];
            
            case 'PUT':
                // Check if order can be modified (only pending orders)
                if ($this->orders[$id]['status'] !== 'pending') {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Only pending orders can be modified'
                    ];
                }
                
                // Update order item
                $oldTotal = $this->orderItems[$itemId]['total'];
                
                if (isset($data['quantity'])) {
                    $this->orderItems[$itemId]['quantity'] = (int) $data['quantity'];
                    $this->orderItems[$itemId]['total'] = $this->orderItems[$itemId]['quantity'] * $this->orderItems[$itemId]['price'];
                }
                
                if (isset($data['price'])) {
                    $this->orderItems[$itemId]['price'] = (float) $data['price'];
                    $this->orderItems[$itemId]['total'] = $this->orderItems[$itemId]['quantity'] * $this->orderItems[$itemId]['price'];
                }
                
                // Update order total
                $this->orders[$id]['total'] = $this->orders[$id]['total'] - $oldTotal + $this->orderItems[$itemId]['total'];
                $this->orders[$id]['updated_at'] = date('Y-m-d H:i:s');
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $this->orderItems[$itemId]
                ];
            
            case 'DELETE':
                // Check if order can be modified (only pending orders)
                if ($this->orders[$id]['status'] !== 'pending') {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Only pending orders can be modified'
                    ];
                }
                
                // Update order total
                $this->orders[$id]['total'] -= $this->orderItems[$itemId]['total'];
                $this->orders[$id]['updated_at'] = date('Y-m-d H:i:s');
                
                // Delete order item
                $orderItem = $this->orderItems[$itemId];
                unset($this->orderItems[$itemId]);
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $orderItem
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
     * Handle requests to the /orders/{id}/status endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleOrderStatus(string $method, array $data, array $headers): array
    {
        // Extract order ID from the endpoint
        $id = isset($data['id']) ? (int) $data['id'] : 0;
        
        // Check if order exists
        if (!isset($this->orders[$id])) {
            return [
                'status' => 'error',
                'code' => 404,
                'message' => 'Order not found'
            ];
        }
        
        switch ($method) {
            case 'PUT':
                // Validate required fields
                if (empty($data['status'])) {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Missing required fields'
                    ];
                }
                
                // Validate status
                if (!isset($this->orderStatuses[$data['status']])) {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Invalid status'
                    ];
                }
                
                $oldStatus = $this->orders[$id]['status'];
                $newStatus = $data['status'];
                
                // Update status
                $this->orders[$id]['status'] = $newStatus;
                $this->orders[$id]['updated_at'] = date('Y-m-d H:i:s');
                
                // If status changed to cancelled, publish event
                if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                    // Get order items
                    $items = array_filter($this->orderItems, function ($item) use ($id) {
                        return $item['order_id'] == $id;
                    });
                    
                    // Publish order-cancelled event
                    if (isset($data['broker']) && $data['broker'] instanceof Broker) {
                        $this->publishEvent($data['broker'], 'order-cancelled', [
                            'order_id' => $id,
                            'user_id' => $this->orders[$id]['user_id'],
                            'total' => $this->orders[$id]['total'],
                            'items' => array_values($items)
                        ]);
                    }
                }
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $this->orders[$id]
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
     * Handle requests to the /statuses endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleStatuses(string $method, array $data, array $headers): array
    {
        switch ($method) {
            case 'GET':
                $statuses = [];
                
                foreach ($this->orderStatuses as $key => $value) {
                    $statuses[] = [
                        'code' => $key,
                        'name' => $value
                    ];
                }
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $statuses
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
     * Handle the payment-processed event
     * 
     * @param array $event The event data
     * @return void
     */
    public function handlePaymentProcessedEvent(array $event): void
    {
        // Update order status based on payment
        if (isset($event['message']['order_id'])) {
            $orderId = $event['message']['order_id'];
            
            if (isset($this->orders[$orderId])) {
                $this->orders[$orderId]['status'] = 'processing';
                $this->orders[$orderId]['updated_at'] = date('Y-m-d H:i:s');
            }
        }
    }
    
    /**
     * Handle the payment-failed event
     * 
     * @param array $event The event data
     * @return void
     */
    public function handlePaymentFailedEvent(array $event): void
    {
        // Update order status based on payment
        if (isset($event['message']['order_id'])) {
            $orderId = $event['message']['order_id'];
            
            if (isset($this->orders[$orderId])) {
                $this->orders[$orderId]['status'] = 'cancelled';
                $this->orders[$orderId]['updated_at'] = date('Y-m-d H:i:s');
            }
        }
    }
}
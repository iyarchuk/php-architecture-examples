<?php

namespace MonolithicArchitecture\Models;

/**
 * Order model represents order data and database interactions
 */
class Order
{
    /**
     * Get an order by ID
     * 
     * @param int $orderId Order ID
     * @return array|null Order data or null if not found
     */
    public function getById(int $orderId): ?array
    {
        // In a real application, we would fetch the order from the database
        // For this example, we'll simulate some orders
        $orders = $this->getAll();
        
        foreach ($orders as $order) {
            if ($order['id'] === $orderId) {
                return $order;
            }
        }

        return null;
    }

    /**
     * Get orders by user ID
     * 
     * @param int $userId User ID
     * @return array List of orders for the user
     */
    public function getByUserId(int $userId): array
    {
        // In a real application, we would fetch orders from the database
        // For this example, we'll simulate some orders
        $orders = $this->getAll();
        $userOrders = [];
        
        foreach ($orders as $order) {
            if ($order['user_id'] === $userId) {
                $userOrders[] = $order;
            }
        }
        
        return $userOrders;
    }

    /**
     * Create a new order
     * 
     * @param array $data Order data
     * @return array Created order data
     */
    public function create(array $data): array
    {
        // In a real application, we would save the order to the database
        // For this example, we'll simulate creating an order
        return [
            'id' => 3, // Simulate a new ID
            'user_id' => $data['user_id'],
            'total' => $data['total'],
            'status' => $data['status'],
            'created_at' => $data['created_at'],
            'updated_at' => $data['updated_at']
        ];
    }

    /**
     * Update an order
     * 
     * @param int $orderId Order ID
     * @param array $data Order data to update
     * @return array Updated order data
     */
    public function update(int $orderId, array $data): array
    {
        // In a real application, we would update the order in the database
        // For this example, we'll simulate an update
        return [
            'id' => $orderId,
            'user_id' => $data['user_id'],
            'total' => $data['total'],
            'status' => $data['status'],
            'created_at' => $data['created_at'],
            'updated_at' => $data['updated_at']
        ];
    }

    /**
     * Delete an order
     * 
     * @param int $orderId Order ID
     * @return bool True if deleted, false otherwise
     */
    public function delete(int $orderId): bool
    {
        // In a real application, we would delete the order from the database
        // For this example, we'll simulate a deletion
        return true;
    }

    /**
     * Get all orders
     * 
     * @return array List of orders
     */
    public function getAll(): array
    {
        // In a real application, we would fetch all orders from the database
        // For this example, we'll simulate some orders
        return [
            [
                'id' => 1,
                'user_id' => 1,
                'total' => 1149.98,
                'status' => 'completed',
                'created_at' => '2023-01-10 10:00:00',
                'updated_at' => '2023-01-10 10:30:00'
            ],
            [
                'id' => 2,
                'user_id' => 1,
                'total' => 699.99,
                'status' => 'pending',
                'created_at' => '2023-01-15 14:00:00',
                'updated_at' => '2023-01-15 14:00:00'
            ]
        ];
    }

    /**
     * Get order items for an order
     * 
     * @param int $orderId Order ID
     * @return array List of order items
     */
    public function getOrderItems(int $orderId): array
    {
        // In a real application, we would fetch order items from the database
        // For this example, we'll simulate some order items
        $orderItems = [
            1 => [
                [
                    'id' => 1,
                    'order_id' => 1,
                    'product_id' => 1,
                    'quantity' => 1,
                    'price' => 999.99,
                    'total' => 999.99
                ],
                [
                    'id' => 2,
                    'order_id' => 1,
                    'product_id' => 3,
                    'quantity' => 1,
                    'price' => 149.99,
                    'total' => 149.99
                ]
            ],
            2 => [
                [
                    'id' => 3,
                    'order_id' => 2,
                    'product_id' => 2,
                    'quantity' => 1,
                    'price' => 699.99,
                    'total' => 699.99
                ]
            ]
        ];
        
        return $orderItems[$orderId] ?? [];
    }

    /**
     * Add an item to an order
     * 
     * @param array $data Order item data
     * @return array Created order item data
     */
    public function addOrderItem(array $data): array
    {
        // In a real application, we would save the order item to the database
        // For this example, we'll simulate creating an order item
        return [
            'id' => 4, // Simulate a new ID
            'order_id' => $data['order_id'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'price' => $data['price'],
            'total' => $data['total']
        ];
    }

    /**
     * Update an order item
     * 
     * @param int $itemId Order item ID
     * @param array $data Order item data to update
     * @return array Updated order item data
     */
    public function updateOrderItem(int $itemId, array $data): array
    {
        // In a real application, we would update the order item in the database
        // For this example, we'll simulate an update
        return [
            'id' => $itemId,
            'order_id' => $data['order_id'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'price' => $data['price'],
            'total' => $data['total']
        ];
    }

    /**
     * Remove an item from an order
     * 
     * @param int $itemId Order item ID
     * @return bool True if removed, false otherwise
     */
    public function removeOrderItem(int $itemId): bool
    {
        // In a real application, we would remove the order item from the database
        // For this example, we'll simulate a removal
        return true;
    }
}
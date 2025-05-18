<?php

namespace MonolithicArchitecture\Services;

use MonolithicArchitecture\Models\Order;
use MonolithicArchitecture\Models\Product;
use MonolithicArchitecture\Models\User;
use MonolithicArchitecture\Utils\Logger;
use MonolithicArchitecture\Utils\EmailSender;

/**
 * OrderService contains business logic for order-related operations
 */
class OrderService
{
    private $orderModel;
    private $productModel;
    private $userModel;
    private $logger;
    private $emailSender;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->userModel = new User();
        $this->logger = new Logger();
        $this->emailSender = new EmailSender();
    }

    /**
     * Get all orders for a user
     * 
     * @param int $userId User ID
     * @return array Response with orders data
     */
    public function getUserOrders(int $userId): array
    {
        $this->logger->info("Getting orders for user: $userId");

        // Check if the user exists
        $user = $this->userModel->getById($userId);
        if (!$user) {
            $this->logger->warning("User not found: $userId");
            return [
                'status' => 'error',
                'message' => 'User not found'
            ];
        }

        // In a real application, we would fetch orders from the database
        // For this example, we'll simulate some orders
        $orders = $this->orderModel->getByUserId($userId);

        $this->logger->info("Retrieved " . count($orders) . " orders for user: $userId");

        return [
            'status' => 'success',
            'data' => $orders
        ];
    }

    /**
     * Get a specific order
     * 
     * @param int $orderId Order ID
     * @return array Response with order data
     */
    public function getOrder(int $orderId): array
    {
        $this->logger->info("Getting order: $orderId");

        // In a real application, we would fetch the order from the database
        // For this example, we'll simulate an order
        $order = $this->orderModel->getById($orderId);

        if (!$order) {
            $this->logger->warning("Order not found: $orderId");
            return [
                'status' => 'error',
                'message' => 'Order not found'
            ];
        }

        // Get the order items
        $items = $this->orderModel->getOrderItems($orderId);

        $this->logger->info("Retrieved order: $orderId with " . count($items) . " items");

        return [
            'status' => 'success',
            'data' => [
                'order' => $order,
                'items' => $items
            ]
        ];
    }

    /**
     * Create a new order
     * 
     * @param array $data Order data
     * @return array Response with created order data
     */
    public function createOrder(array $data): array
    {
        $this->logger->info("Creating new order for user: {$data['user_id']}");

        // Check if the user exists
        $user = $this->userModel->getById($data['user_id']);
        if (!$user) {
            $this->logger->warning("User not found: {$data['user_id']}");
            return [
                'status' => 'error',
                'message' => 'User not found'
            ];
        }

        // Validate order items
        if (!isset($data['items']) || empty($data['items'])) {
            $this->logger->warning("No items in order for user: {$data['user_id']}");
            return [
                'status' => 'error',
                'message' => 'Order must contain at least one item'
            ];
        }

        // Calculate order total
        $total = 0;
        $items = [];
        foreach ($data['items'] as $item) {
            // Check if the product exists
            $product = $this->productModel->getById($item['product_id']);
            if (!$product) {
                $this->logger->warning("Product not found: {$item['product_id']}");
                return [
                    'status' => 'error',
                    'message' => "Product not found: {$item['product_id']}"
                ];
            }

            // Calculate item total
            $itemTotal = $product['price'] * $item['quantity'];
            $total += $itemTotal;

            // Add item to the list
            $items[] = [
                'product_id' => $product['id'],
                'quantity' => $item['quantity'],
                'price' => $product['price'],
                'total' => $itemTotal
            ];
        }

        // Create the order
        $orderData = [
            'user_id' => $data['user_id'],
            'total' => $total,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // In a real application, we would save the order to the database
        // For this example, we'll simulate creating an order
        $order = $this->orderModel->create($orderData);

        // Save the order items
        foreach ($items as &$item) {
            $item['order_id'] = $order['id'];
            $this->orderModel->addOrderItem($item);
        }

        $this->logger->info("Order created: {$order['id']} with total: {$order['total']}");

        // Send order confirmation email
        $this->emailSender->send(
            $user['email'],
            'Order Confirmation',
            "Your order #{$order['id']} has been placed successfully. Total: \${$order['total']}"
        );

        return [
            'status' => 'success',
            'data' => [
                'order' => $order,
                'items' => $items
            ]
        ];
    }

    /**
     * Update an order
     * 
     * @param int $orderId Order ID
     * @param array $data Order data to update
     * @return array Response with updated order data
     */
    public function updateOrder(int $orderId, array $data): array
    {
        $this->logger->info("Updating order: $orderId");

        // In a real application, we would update the order in the database
        // For this example, we'll simulate an update
        $order = $this->orderModel->getById($orderId);

        if (!$order) {
            $this->logger->warning("Order not found: $orderId");
            return [
                'status' => 'error',
                'message' => 'Order not found'
            ];
        }

        // Update the order data
        foreach ($data as $key => $value) {
            if (isset($order[$key]) && $key !== 'id' && $key !== 'user_id') {
                $order[$key] = $value;
            }
        }

        // Update the timestamp
        $order['updated_at'] = date('Y-m-d H:i:s');

        // Save the updated order
        $updatedOrder = $this->orderModel->update($orderId, $order);

        // Get the order items
        $items = $this->orderModel->getOrderItems($orderId);

        $this->logger->info("Order updated: $orderId");

        // Send order update email
        $user = $this->userModel->getById($order['user_id']);
        if ($user) {
            $this->emailSender->send(
                $user['email'],
                'Order Updated',
                "Your order #{$order['id']} has been updated. Status: {$order['status']}"
            );
        }

        return [
            'status' => 'success',
            'data' => [
                'order' => $updatedOrder,
                'items' => $items
            ]
        ];
    }

    /**
     * Cancel an order
     * 
     * @param int $orderId Order ID
     * @return array Response with cancellation status
     */
    public function cancelOrder(int $orderId): array
    {
        $this->logger->info("Cancelling order: $orderId");

        // In a real application, we would update the order in the database
        // For this example, we'll simulate a cancellation
        $order = $this->orderModel->getById($orderId);

        if (!$order) {
            $this->logger->warning("Order not found: $orderId");
            return [
                'status' => 'error',
                'message' => 'Order not found'
            ];
        }

        // Check if the order can be cancelled
        if ($order['status'] === 'shipped' || $order['status'] === 'delivered') {
            $this->logger->warning("Cannot cancel order: $orderId, status: {$order['status']}");
            return [
                'status' => 'error',
                'message' => "Cannot cancel order with status: {$order['status']}"
            ];
        }

        // Update the order status
        $order['status'] = 'cancelled';
        $order['updated_at'] = date('Y-m-d H:i:s');

        // Save the updated order
        $updatedOrder = $this->orderModel->update($orderId, $order);

        $this->logger->info("Order cancelled: $orderId");

        // Send order cancellation email
        $user = $this->userModel->getById($order['user_id']);
        if ($user) {
            $this->emailSender->send(
                $user['email'],
                'Order Cancelled',
                "Your order #{$order['id']} has been cancelled."
            );
        }

        return [
            'status' => 'success',
            'message' => 'Order cancelled successfully',
            'data' => $updatedOrder
        ];
    }
}
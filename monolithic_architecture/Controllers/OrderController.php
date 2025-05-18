<?php

namespace MonolithicArchitecture\Controllers;

use MonolithicArchitecture\Services\OrderService;

/**
 * OrderController handles order-related HTTP requests
 */
class OrderController
{
    private $orderService;

    public function __construct()
    {
        $this->orderService = new OrderService();
    }

    /**
     * Get all orders for a user
     * 
     * @param int $userId User ID
     * @param array $headers Request headers
     * @return array Response with orders data
     */
    public function getUserOrders(int $userId, array $headers): array
    {
        // Validate authentication
        if (!$this->isAuthenticated($headers)) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized'
            ];
        }

        // Call the service to get all orders for the user
        return $this->orderService->getUserOrders($userId);
    }

    /**
     * Get a specific order
     * 
     * @param int $orderId Order ID
     * @param array $headers Request headers
     * @return array Response with order data
     */
    public function getOrder(int $orderId, array $headers): array
    {
        // Validate authentication
        if (!$this->isAuthenticated($headers)) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized'
            ];
        }

        // Call the service to get the order
        return $this->orderService->getOrder($orderId);
    }

    /**
     * Create a new order
     * 
     * @param array $data Order data
     * @param array $headers Request headers
     * @return array Response with created order data
     */
    public function createOrder(array $data, array $headers): array
    {
        // Validate authentication
        if (!$this->isAuthenticated($headers)) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized'
            ];
        }

        // Validate input
        if (!isset($data['user_id']) || !isset($data['items']) || empty($data['items'])) {
            return [
                'status' => 'error',
                'message' => 'User ID and at least one item are required'
            ];
        }

        // Call the service to create the order
        return $this->orderService->createOrder($data);
    }

    /**
     * Update an order
     * 
     * @param int $orderId Order ID
     * @param array $data Order data to update
     * @param array $headers Request headers
     * @return array Response with updated order data
     */
    public function updateOrder(int $orderId, array $data, array $headers): array
    {
        // Validate authentication
        if (!$this->isAuthenticated($headers)) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized'
            ];
        }

        // Call the service to update the order
        return $this->orderService->updateOrder($orderId, $data);
    }

    /**
     * Cancel an order
     * 
     * @param int $orderId Order ID
     * @param array $headers Request headers
     * @return array Response with cancellation status
     */
    public function cancelOrder(int $orderId, array $headers): array
    {
        // Validate authentication
        if (!$this->isAuthenticated($headers)) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized'
            ];
        }

        // Call the service to cancel the order
        return $this->orderService->cancelOrder($orderId);
    }

    /**
     * Check if the request is authenticated
     * 
     * @param array $headers Request headers
     * @return bool True if authenticated, false otherwise
     */
    private function isAuthenticated(array $headers): bool
    {
        // Check if the Authorization header is present
        if (!isset($headers['Authorization'])) {
            return false;
        }

        // In a real application, we would validate the token
        // For this example, we'll just check if it starts with "Bearer "
        return strpos($headers['Authorization'], 'Bearer ') === 0;
    }
}
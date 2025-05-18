<?php

namespace MonolithicArchitecture\Controllers;

use MonolithicArchitecture\Services\PaymentService;

/**
 * PaymentController handles payment-related HTTP requests
 */
class PaymentController
{
    private $paymentService;

    public function __construct()
    {
        $this->paymentService = new PaymentService();
    }

    /**
     * Get payment methods for a user
     * 
     * @param int $userId User ID
     * @param array $headers Request headers
     * @return array Response with payment methods data
     */
    public function getPaymentMethods(int $userId, array $headers): array
    {
        // Validate authentication
        if (!$this->isAuthenticated($headers)) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized'
            ];
        }

        // Call the service to get payment methods
        return $this->paymentService->getPaymentMethods($userId);
    }

    /**
     * Add a payment method for a user
     * 
     * @param int $userId User ID
     * @param array $data Payment method data
     * @param array $headers Request headers
     * @return array Response with added payment method data
     */
    public function addPaymentMethod(int $userId, array $data, array $headers): array
    {
        // Validate authentication
        if (!$this->isAuthenticated($headers)) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized'
            ];
        }

        // Validate input
        if (!isset($data['type'])) {
            return [
                'status' => 'error',
                'message' => 'Payment method type is required'
            ];
        }

        // Call the service to add the payment method
        return $this->paymentService->addPaymentMethod($userId, $data);
    }

    /**
     * Process a payment
     * 
     * @param array $data Payment data
     * @param array $headers Request headers
     * @return array Response with payment transaction data
     */
    public function processPayment(array $data, array $headers): array
    {
        // Validate authentication
        if (!$this->isAuthenticated($headers)) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized'
            ];
        }

        // Validate input
        if (!isset($data['user_id']) || !isset($data['order_id']) || 
            !isset($data['payment_method_id']) || !isset($data['amount'])) {
            return [
                'status' => 'error',
                'message' => 'User ID, order ID, payment method ID, and amount are required'
            ];
        }

        // Call the service to process the payment
        return $this->paymentService->processPayment($data);
    }

    /**
     * Get payment transactions for a user
     * 
     * @param int $userId User ID
     * @param array $headers Request headers
     * @return array Response with payment transactions data
     */
    public function getTransactions(int $userId, array $headers): array
    {
        // Validate authentication
        if (!$this->isAuthenticated($headers)) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized'
            ];
        }

        // Call the service to get transactions
        return $this->paymentService->getTransactions($userId);
    }

    /**
     * Get a specific payment transaction
     * 
     * @param int $transactionId Transaction ID
     * @param array $headers Request headers
     * @return array Response with payment transaction data
     */
    public function getTransaction(int $transactionId, array $headers): array
    {
        // Validate authentication
        if (!$this->isAuthenticated($headers)) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized'
            ];
        }

        // Call the service to get the transaction
        return $this->paymentService->getTransaction($transactionId);
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
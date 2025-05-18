<?php

namespace MonolithicArchitecture\Services;

use MonolithicArchitecture\Models\Payment;
use MonolithicArchitecture\Models\Order;
use MonolithicArchitecture\Models\User;
use MonolithicArchitecture\Utils\Logger;
use MonolithicArchitecture\Utils\EmailSender;

/**
 * PaymentService contains business logic for payment-related operations
 */
class PaymentService
{
    private $paymentModel;
    private $orderModel;
    private $userModel;
    private $logger;
    private $emailSender;

    public function __construct()
    {
        $this->paymentModel = new Payment();
        $this->orderModel = new Order();
        $this->userModel = new User();
        $this->logger = new Logger();
        $this->emailSender = new EmailSender();
    }

    /**
     * Get payment methods for a user
     * 
     * @param int $userId User ID
     * @return array Response with payment methods data
     */
    public function getPaymentMethods(int $userId): array
    {
        $this->logger->info("Getting payment methods for user: $userId");

        // Check if the user exists
        $user = $this->userModel->getById($userId);
        if (!$user) {
            $this->logger->warning("User not found: $userId");
            return [
                'status' => 'error',
                'message' => 'User not found'
            ];
        }

        // In a real application, we would fetch payment methods from the database
        // For this example, we'll simulate some payment methods
        $paymentMethods = $this->paymentModel->getPaymentMethodsByUserId($userId);

        $this->logger->info("Retrieved " . count($paymentMethods) . " payment methods for user: $userId");

        return [
            'status' => 'success',
            'data' => $paymentMethods
        ];
    }

    /**
     * Add a payment method for a user
     * 
     * @param int $userId User ID
     * @param array $data Payment method data
     * @return array Response with added payment method data
     */
    public function addPaymentMethod(int $userId, array $data): array
    {
        $this->logger->info("Adding payment method for user: $userId");

        // Check if the user exists
        $user = $this->userModel->getById($userId);
        if (!$user) {
            $this->logger->warning("User not found: $userId");
            return [
                'status' => 'error',
                'message' => 'User not found'
            ];
        }

        // Validate payment method data
        if (!isset($data['type'])) {
            $this->logger->warning("Invalid payment method data: type is required");
            return [
                'status' => 'error',
                'message' => 'Payment method type is required'
            ];
        }

        // Check if this is the first payment method for the user
        $existingMethods = $this->paymentModel->getPaymentMethodsByUserId($userId);
        $isDefault = empty($existingMethods) || (isset($data['is_default']) && $data['is_default']);

        // Prepare payment method data
        $paymentMethodData = [
            'user_id' => $userId,
            'type' => $data['type'],
            'is_default' => $isDefault,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Add additional data based on the payment method type
        if ($data['type'] === 'credit_card' || $data['type'] === 'debit_card') {
            // In a real application, we would validate and store card details securely
            // For this example, we'll just store a masked card number
            $paymentMethodData['card_last4'] = substr($data['card_number'] ?? '0000000000000000', -4);
            $paymentMethodData['card_expiry'] = $data['card_expiry'] ?? '12/25';
        } elseif ($data['type'] === 'paypal') {
            $paymentMethodData['paypal_email'] = $data['paypal_email'] ?? $user['email'];
        } elseif ($data['type'] === 'bank_transfer') {
            $paymentMethodData['bank_account'] = $data['bank_account'] ?? '****1234';
            $paymentMethodData['bank_name'] = $data['bank_name'] ?? 'Example Bank';
        }

        // In a real application, we would save the payment method to the database
        // For this example, we'll simulate adding a payment method
        $paymentMethod = $this->paymentModel->createPaymentMethod($paymentMethodData);

        // If this is the default payment method, update other methods
        if ($isDefault) {
            $this->paymentModel->updateOtherPaymentMethodsToNonDefault($userId, $paymentMethod['id']);
        }

        $this->logger->info("Payment method added for user: $userId, ID: {$paymentMethod['id']}");

        return [
            'status' => 'success',
            'data' => $paymentMethod
        ];
    }

    /**
     * Process a payment
     * 
     * @param array $data Payment data
     * @return array Response with payment transaction data
     */
    public function processPayment(array $data): array
    {
        $this->logger->info("Processing payment for order: {$data['order_id']}");

        // Check if the user exists
        $user = $this->userModel->getById($data['user_id']);
        if (!$user) {
            $this->logger->warning("User not found: {$data['user_id']}");
            return [
                'status' => 'error',
                'message' => 'User not found'
            ];
        }

        // Check if the order exists
        $order = $this->orderModel->getById($data['order_id']);
        if (!$order) {
            $this->logger->warning("Order not found: {$data['order_id']}");
            return [
                'status' => 'error',
                'message' => 'Order not found'
            ];
        }

        // Check if the payment method exists
        $paymentMethod = $this->paymentModel->getPaymentMethodById($data['payment_method_id']);
        if (!$paymentMethod) {
            $this->logger->warning("Payment method not found: {$data['payment_method_id']}");
            return [
                'status' => 'error',
                'message' => 'Payment method not found'
            ];
        }

        // Validate the amount
        if ($data['amount'] <= 0) {
            $this->logger->warning("Invalid payment amount: {$data['amount']}");
            return [
                'status' => 'error',
                'message' => 'Payment amount must be greater than zero'
            ];
        }

        // In a real application, we would process the payment through a payment gateway
        // For this example, we'll simulate a successful payment
        $transactionData = [
            'user_id' => $data['user_id'],
            'order_id' => $data['order_id'],
            'payment_method_id' => $data['payment_method_id'],
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'USD',
            'status' => 'completed',
            'reference' => 'TX' . time() . rand(1000, 9999),
            'description' => $data['description'] ?? "Payment for Order #{$data['order_id']}",
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Save the transaction
        $transaction = $this->paymentModel->createTransaction($transactionData);

        // Update the order status
        $orderData = [
            'status' => 'paid',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->orderModel->update($data['order_id'], $orderData);

        $this->logger->info("Payment processed successfully for order: {$data['order_id']}, transaction: {$transaction['id']}");

        // Send payment confirmation email
        $this->emailSender->send(
            $user['email'],
            'Payment Confirmation',
            "Your payment for order #{$data['order_id']} has been processed successfully. " .
            "Amount: \${$data['amount']} {$transaction['currency']}. " .
            "Transaction reference: {$transaction['reference']}"
        );

        return [
            'status' => 'success',
            'data' => $transaction
        ];
    }

    /**
     * Get payment transactions for a user
     * 
     * @param int $userId User ID
     * @return array Response with payment transactions data
     */
    public function getTransactions(int $userId): array
    {
        $this->logger->info("Getting payment transactions for user: $userId");

        // Check if the user exists
        $user = $this->userModel->getById($userId);
        if (!$user) {
            $this->logger->warning("User not found: $userId");
            return [
                'status' => 'error',
                'message' => 'User not found'
            ];
        }

        // In a real application, we would fetch transactions from the database
        // For this example, we'll simulate some transactions
        $transactions = $this->paymentModel->getTransactionsByUserId($userId);

        $this->logger->info("Retrieved " . count($transactions) . " transactions for user: $userId");

        return [
            'status' => 'success',
            'data' => $transactions
        ];
    }

    /**
     * Get a specific payment transaction
     * 
     * @param int $transactionId Transaction ID
     * @return array Response with payment transaction data
     */
    public function getTransaction(int $transactionId): array
    {
        $this->logger->info("Getting payment transaction: $transactionId");

        // In a real application, we would fetch the transaction from the database
        // For this example, we'll simulate a transaction
        $transaction = $this->paymentModel->getTransactionById($transactionId);

        if (!$transaction) {
            $this->logger->warning("Transaction not found: $transactionId");
            return [
                'status' => 'error',
                'message' => 'Transaction not found'
            ];
        }

        $this->logger->info("Retrieved transaction: $transactionId");

        return [
            'status' => 'success',
            'data' => $transaction
        ];
    }
}
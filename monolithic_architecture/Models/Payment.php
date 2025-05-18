<?php

namespace MonolithicArchitecture\Models;

/**
 * Payment model represents payment data and database interactions
 */
class Payment
{
    /**
     * Get payment methods for a user
     * 
     * @param int $userId User ID
     * @return array List of payment methods
     */
    public function getPaymentMethodsByUserId(int $userId): array
    {
        // In a real application, we would fetch payment methods from the database
        // For this example, we'll simulate some payment methods
        if ($userId === 1) {
            return [
                [
                    'id' => 1,
                    'user_id' => 1,
                    'type' => 'credit_card',
                    'card_last4' => '1234',
                    'card_expiry' => '12/25',
                    'is_default' => true,
                    'created_at' => '2023-01-05 00:00:00'
                ],
                [
                    'id' => 2,
                    'user_id' => 1,
                    'type' => 'paypal',
                    'paypal_email' => 'john.doe@example.com',
                    'is_default' => false,
                    'created_at' => '2023-01-06 00:00:00'
                ]
            ];
        }
        
        return [];
    }

    /**
     * Get a payment method by ID
     * 
     * @param int $paymentMethodId Payment method ID
     * @return array|null Payment method data or null if not found
     */
    public function getPaymentMethodById(int $paymentMethodId): ?array
    {
        // In a real application, we would fetch the payment method from the database
        // For this example, we'll simulate some payment methods
        $paymentMethods = [
            1 => [
                'id' => 1,
                'user_id' => 1,
                'type' => 'credit_card',
                'card_last4' => '1234',
                'card_expiry' => '12/25',
                'is_default' => true,
                'created_at' => '2023-01-05 00:00:00'
            ],
            2 => [
                'id' => 2,
                'user_id' => 1,
                'type' => 'paypal',
                'paypal_email' => 'john.doe@example.com',
                'is_default' => false,
                'created_at' => '2023-01-06 00:00:00'
            ]
        ];
        
        return $paymentMethods[$paymentMethodId] ?? null;
    }

    /**
     * Create a new payment method
     * 
     * @param array $data Payment method data
     * @return array Created payment method data
     */
    public function createPaymentMethod(array $data): array
    {
        // In a real application, we would save the payment method to the database
        // For this example, we'll simulate creating a payment method
        return array_merge(
            [
                'id' => 3, // Simulate a new ID
            ],
            $data
        );
    }

    /**
     * Update a payment method
     * 
     * @param int $paymentMethodId Payment method ID
     * @param array $data Payment method data to update
     * @return array Updated payment method data
     */
    public function updatePaymentMethod(int $paymentMethodId, array $data): array
    {
        // In a real application, we would update the payment method in the database
        // For this example, we'll simulate an update
        return array_merge(
            [
                'id' => $paymentMethodId,
            ],
            $data,
            [
                'updated_at' => date('Y-m-d H:i:s')
            ]
        );
    }

    /**
     * Delete a payment method
     * 
     * @param int $paymentMethodId Payment method ID
     * @return bool True if deleted, false otherwise
     */
    public function deletePaymentMethod(int $paymentMethodId): bool
    {
        // In a real application, we would delete the payment method from the database
        // For this example, we'll simulate a deletion
        return true;
    }

    /**
     * Update other payment methods to non-default
     * 
     * @param int $userId User ID
     * @param int $defaultPaymentMethodId Default payment method ID
     * @return bool True if updated, false otherwise
     */
    public function updateOtherPaymentMethodsToNonDefault(int $userId, int $defaultPaymentMethodId): bool
    {
        // In a real application, we would update payment methods in the database
        // For this example, we'll simulate an update
        return true;
    }

    /**
     * Get payment transactions for a user
     * 
     * @param int $userId User ID
     * @return array List of payment transactions
     */
    public function getTransactionsByUserId(int $userId): array
    {
        // In a real application, we would fetch transactions from the database
        // For this example, we'll simulate some transactions
        if ($userId === 1) {
            return [
                [
                    'id' => 1,
                    'user_id' => 1,
                    'order_id' => 1,
                    'payment_method_id' => 1,
                    'amount' => 1149.98,
                    'currency' => 'USD',
                    'status' => 'completed',
                    'reference' => 'TX123456789',
                    'description' => 'Payment for Order #1',
                    'created_at' => '2023-01-10 10:15:00'
                ],
                [
                    'id' => 2,
                    'user_id' => 1,
                    'order_id' => 2,
                    'payment_method_id' => 1,
                    'amount' => 699.99,
                    'currency' => 'USD',
                    'status' => 'pending',
                    'reference' => 'TX987654321',
                    'description' => 'Payment for Order #2',
                    'created_at' => '2023-01-15 14:30:00'
                ]
            ];
        }
        
        return [];
    }

    /**
     * Get a payment transaction by ID
     * 
     * @param int $transactionId Transaction ID
     * @return array|null Transaction data or null if not found
     */
    public function getTransactionById(int $transactionId): ?array
    {
        // In a real application, we would fetch the transaction from the database
        // For this example, we'll simulate some transactions
        $transactions = [
            1 => [
                'id' => 1,
                'user_id' => 1,
                'order_id' => 1,
                'payment_method_id' => 1,
                'amount' => 1149.98,
                'currency' => 'USD',
                'status' => 'completed',
                'reference' => 'TX123456789',
                'description' => 'Payment for Order #1',
                'created_at' => '2023-01-10 10:15:00'
            ],
            2 => [
                'id' => 2,
                'user_id' => 1,
                'order_id' => 2,
                'payment_method_id' => 1,
                'amount' => 699.99,
                'currency' => 'USD',
                'status' => 'pending',
                'reference' => 'TX987654321',
                'description' => 'Payment for Order #2',
                'created_at' => '2023-01-15 14:30:00'
            ]
        ];
        
        return $transactions[$transactionId] ?? null;
    }

    /**
     * Create a new payment transaction
     * 
     * @param array $data Transaction data
     * @return array Created transaction data
     */
    public function createTransaction(array $data): array
    {
        // In a real application, we would save the transaction to the database
        // For this example, we'll simulate creating a transaction
        return array_merge(
            [
                'id' => 3, // Simulate a new ID
            ],
            $data
        );
    }

    /**
     * Update a payment transaction
     * 
     * @param int $transactionId Transaction ID
     * @param array $data Transaction data to update
     * @return array Updated transaction data
     */
    public function updateTransaction(int $transactionId, array $data): array
    {
        // In a real application, we would update the transaction in the database
        // For this example, we'll simulate an update
        return array_merge(
            [
                'id' => $transactionId,
            ],
            $data,
            [
                'updated_at' => date('Y-m-d H:i:s')
            ]
        );
    }

    /**
     * Get transactions for an order
     * 
     * @param int $orderId Order ID
     * @return array List of transactions for the order
     */
    public function getTransactionsByOrderId(int $orderId): array
    {
        // In a real application, we would fetch transactions from the database
        // For this example, we'll simulate some transactions
        $transactions = $this->getAllTransactions();
        $orderTransactions = [];
        
        foreach ($transactions as $transaction) {
            if ($transaction['order_id'] === $orderId) {
                $orderTransactions[] = $transaction;
            }
        }
        
        return $orderTransactions;
    }

    /**
     * Get all payment transactions
     * 
     * @return array List of all transactions
     */
    public function getAllTransactions(): array
    {
        // In a real application, we would fetch all transactions from the database
        // For this example, we'll simulate some transactions
        return [
            [
                'id' => 1,
                'user_id' => 1,
                'order_id' => 1,
                'payment_method_id' => 1,
                'amount' => 1149.98,
                'currency' => 'USD',
                'status' => 'completed',
                'reference' => 'TX123456789',
                'description' => 'Payment for Order #1',
                'created_at' => '2023-01-10 10:15:00'
            ],
            [
                'id' => 2,
                'user_id' => 1,
                'order_id' => 2,
                'payment_method_id' => 1,
                'amount' => 699.99,
                'currency' => 'USD',
                'status' => 'pending',
                'reference' => 'TX987654321',
                'description' => 'Payment for Order #2',
                'created_at' => '2023-01-15 14:30:00'
            ]
        ];
    }
}
<?php

namespace MicroservicesArchitecture\Services\PaymentService;

use MicroservicesArchitecture\Services\BaseService;
use MicroservicesArchitecture\MessageBroker\Broker;

/**
 * PaymentService
 * 
 * This class implements the payment processing microservice.
 * It provides endpoints for processing payments, managing payment methods, and viewing transactions.
 */
class PaymentService extends BaseService
{
    /**
     * @var array The payment methods database (in-memory for this example)
     */
    private array $paymentMethods = [];

    /**
     * @var array The transactions database (in-memory for this example)
     */
    private array $transactions = [];

    /**
     * @var array The payment statuses
     */
    private array $paymentStatuses = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'refunded' => 'Refunded'
    ];

    /**
     * @var array The payment method types
     */
    private array $paymentMethodTypes = [
        'credit_card' => 'Credit Card',
        'debit_card' => 'Debit Card',
        'paypal' => 'PayPal',
        'bank_transfer' => 'Bank Transfer'
    ];

    /**
     * Constructor
     * 
     * @param string $url The URL of the service
     */
    public function __construct(string $url = 'http://payment-service.example.com')
    {
        parent::__construct('payment-service', $url);

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
            '/payment-methods' => [
                'methods' => ['GET', 'POST'],
                'handler' => [$this, 'handlePaymentMethods']
            ],
            '/payment-methods/{id}' => [
                'methods' => ['GET', 'PUT', 'DELETE'],
                'handler' => [$this, 'handlePaymentMethod']
            ],
            '/payment-methods/user/{userId}' => [
                'methods' => ['GET'],
                'handler' => [$this, 'handleUserPaymentMethods']
            ],
            '/transactions' => [
                'methods' => ['GET', 'POST'],
                'handler' => [$this, 'handleTransactions']
            ],
            '/transactions/{id}' => [
                'methods' => ['GET'],
                'handler' => [$this, 'handleTransaction']
            ],
            '/transactions/user/{userId}' => [
                'methods' => ['GET'],
                'handler' => [$this, 'handleUserTransactions']
            ],
            '/transactions/order/{orderId}' => [
                'methods' => ['GET'],
                'handler' => [$this, 'handleOrderTransactions']
            ],
            '/process-payment' => [
                'methods' => ['POST'],
                'handler' => [$this, 'handleProcessPayment']
            ],
            '/refund' => [
                'methods' => ['POST'],
                'handler' => [$this, 'handleRefund']
            ],
            '/payment-method-types' => [
                'methods' => ['GET'],
                'handler' => [$this, 'handlePaymentMethodTypes']
            ],
            '/payment-statuses' => [
                'methods' => ['GET'],
                'handler' => [$this, 'handlePaymentStatuses']
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
        // Sample payment methods
        $this->paymentMethods = [
            1 => [
                'id' => 1,
                'user_id' => 1,
                'type' => 'credit_card',
                'details' => [
                    'card_number' => '**** **** **** 1234',
                    'expiry_month' => 12,
                    'expiry_year' => 2025,
                    'cardholder_name' => 'John Doe'
                ],
                'is_default' => true,
                'created_at' => date('Y-m-d H:i:s', strtotime('-30 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-30 days'))
            ],
            2 => [
                'id' => 2,
                'user_id' => 2,
                'type' => 'paypal',
                'details' => [
                    'email' => 'jane.smith@example.com'
                ],
                'is_default' => true,
                'created_at' => date('Y-m-d H:i:s', strtotime('-15 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-15 days'))
            ],
            3 => [
                'id' => 3,
                'user_id' => 1,
                'type' => 'debit_card',
                'details' => [
                    'card_number' => '**** **** **** 5678',
                    'expiry_month' => 6,
                    'expiry_year' => 2024,
                    'cardholder_name' => 'John Doe'
                ],
                'is_default' => false,
                'created_at' => date('Y-m-d H:i:s', strtotime('-10 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-10 days'))
            ]
        ];

        // Sample transactions
        $this->transactions = [
            1 => [
                'id' => 1,
                'user_id' => 1,
                'order_id' => 1,
                'payment_method_id' => 1,
                'amount' => 699.99,
                'currency' => 'USD',
                'status' => 'completed',
                'reference' => 'TXN-' . uniqid(),
                'description' => 'Payment for Order #1',
                'created_at' => date('Y-m-d H:i:s', strtotime('-7 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-7 days'))
            ],
            2 => [
                'id' => 2,
                'user_id' => 2,
                'order_id' => 2,
                'payment_method_id' => 2,
                'amount' => 1299.99,
                'currency' => 'USD',
                'status' => 'processing',
                'reference' => 'TXN-' . uniqid(),
                'description' => 'Payment for Order #2',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            3 => [
                'id' => 3,
                'user_id' => 1,
                'order_id' => 3,
                'payment_method_id' => 3,
                'amount' => 69.98,
                'currency' => 'USD',
                'status' => 'pending',
                'reference' => 'TXN-' . uniqid(),
                'description' => 'Payment for Order #3',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * Handle requests to the /payment-methods endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handlePaymentMethods(string $method, array $data, array $headers): array
    {
        switch ($method) {
            case 'GET':
                // This endpoint is for admin use only
                if (!$this->isAdmin($headers)) {
                    return [
                        'status' => 'error',
                        'code' => 403,
                        'message' => 'Forbidden'
                    ];
                }

                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => array_values($this->paymentMethods)
                ];

            case 'POST':
                // Validate required fields
                if (empty($data['user_id']) || empty($data['type']) || empty($data['details'])) {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Missing required fields'
                    ];
                }

                // Validate payment method type
                if (!isset($this->paymentMethodTypes[$data['type']])) {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Invalid payment method type'
                    ];
                }

                // Create new payment method
                $id = count($this->paymentMethods) + 1;
                $paymentMethod = [
                    'id' => $id,
                    'user_id' => (int) $data['user_id'],
                    'type' => $data['type'],
                    'details' => $data['details'],
                    'is_default' => $data['is_default'] ?? false,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // If this is the default payment method, update other payment methods
                if ($paymentMethod['is_default']) {
                    foreach ($this->paymentMethods as $key => $method) {
                        if ($method['user_id'] === $paymentMethod['user_id']) {
                            $this->paymentMethods[$key]['is_default'] = false;
                        }
                    }
                }

                $this->paymentMethods[$id] = $paymentMethod;

                return [
                    'status' => 'success',
                    'code' => 201,
                    'data' => $paymentMethod
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
     * Handle requests to the /payment-methods/{id} endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handlePaymentMethod(string $method, array $data, array $headers): array
    {
        // Extract payment method ID from the endpoint
        $id = isset($data['id']) ? (int) $data['id'] : 0;

        // Check if payment method exists
        if (!isset($this->paymentMethods[$id])) {
            return [
                'status' => 'error',
                'code' => 404,
                'message' => 'Payment method not found'
            ];
        }

        // Check if the user is authorized to access this payment method
        if (!$this->isAdmin($headers) && !$this->isCurrentUser($headers, $this->paymentMethods[$id]['user_id'])) {
            return [
                'status' => 'error',
                'code' => 403,
                'message' => 'Forbidden'
            ];
        }

        switch ($method) {
            case 'GET':
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $this->paymentMethods[$id]
                ];

            case 'PUT':
                // Update payment method
                if (isset($data['type'])) {
                    // Validate payment method type
                    if (!isset($this->paymentMethodTypes[$data['type']])) {
                        return [
                            'status' => 'error',
                            'code' => 400,
                            'message' => 'Invalid payment method type'
                        ];
                    }

                    $this->paymentMethods[$id]['type'] = $data['type'];
                }

                if (isset($data['details'])) {
                    $this->paymentMethods[$id]['details'] = $data['details'];
                }

                if (isset($data['is_default'])) {
                    $isDefault = (bool) $data['is_default'];
                    $this->paymentMethods[$id]['is_default'] = $isDefault;

                    // If this is the default payment method, update other payment methods
                    if ($isDefault) {
                        $userId = $this->paymentMethods[$id]['user_id'];
                        foreach ($this->paymentMethods as $key => $method) {
                            if ($key !== $id && $method['user_id'] === $userId) {
                                $this->paymentMethods[$key]['is_default'] = false;
                            }
                        }
                    }
                }

                $this->paymentMethods[$id]['updated_at'] = date('Y-m-d H:i:s');

                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $this->paymentMethods[$id]
                ];

            case 'DELETE':
                // Check if payment method is used in any transactions
                foreach ($this->transactions as $transaction) {
                    if ($transaction['payment_method_id'] === $id) {
                        return [
                            'status' => 'error',
                            'code' => 400,
                            'message' => 'Cannot delete payment method that is used in transactions'
                        ];
                    }
                }

                // Delete payment method
                $paymentMethod = $this->paymentMethods[$id];
                unset($this->paymentMethods[$id]);

                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $paymentMethod
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
     * Handle requests to the /payment-methods/user/{userId} endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleUserPaymentMethods(string $method, array $data, array $headers): array
    {
        // Extract user ID from the endpoint
        $userId = isset($data['userId']) ? (int) $data['userId'] : 0;

        // Check if the user is authorized to access this user's payment methods
        if (!$this->isAdmin($headers) && !$this->isCurrentUser($headers, $userId)) {
            return [
                'status' => 'error',
                'code' => 403,
                'message' => 'Forbidden'
            ];
        }

        switch ($method) {
            case 'GET':
                // Filter payment methods by user ID
                $paymentMethods = array_filter($this->paymentMethods, function ($method) use ($userId) {
                    return $method['user_id'] === $userId;
                });

                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => array_values($paymentMethods)
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
     * Handle requests to the /transactions endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleTransactions(string $method, array $data, array $headers): array
    {
        switch ($method) {
            case 'GET':
                // This endpoint is for admin use only
                if (!$this->isAdmin($headers)) {
                    return [
                        'status' => 'error',
                        'code' => 403,
                        'message' => 'Forbidden'
                    ];
                }

                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => array_values($this->transactions)
                ];

            case 'POST':
                // This endpoint is for internal use only
                return [
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Forbidden'
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
     * Handle requests to the /transactions/{id} endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleTransaction(string $method, array $data, array $headers): array
    {
        // Extract transaction ID from the endpoint
        $id = isset($data['id']) ? (int) $data['id'] : 0;

        // Check if transaction exists
        if (!isset($this->transactions[$id])) {
            return [
                'status' => 'error',
                'code' => 404,
                'message' => 'Transaction not found'
            ];
        }

        // Check if the user is authorized to access this transaction
        if (!$this->isAdmin($headers) && !$this->isCurrentUser($headers, $this->transactions[$id]['user_id'])) {
            return [
                'status' => 'error',
                'code' => 403,
                'message' => 'Forbidden'
            ];
        }

        switch ($method) {
            case 'GET':
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $this->transactions[$id]
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
     * Handle requests to the /transactions/user/{userId} endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleUserTransactions(string $method, array $data, array $headers): array
    {
        // Extract user ID from the endpoint
        $userId = isset($data['userId']) ? (int) $data['userId'] : 0;

        // Check if the user is authorized to access this user's transactions
        if (!$this->isAdmin($headers) && !$this->isCurrentUser($headers, $userId)) {
            return [
                'status' => 'error',
                'code' => 403,
                'message' => 'Forbidden'
            ];
        }

        switch ($method) {
            case 'GET':
                // Filter transactions by user ID
                $transactions = array_filter($this->transactions, function ($transaction) use ($userId) {
                    return $transaction['user_id'] === $userId;
                });

                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => array_values($transactions)
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
     * Handle requests to the /transactions/order/{orderId} endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleOrderTransactions(string $method, array $data, array $headers): array
    {
        // Extract order ID from the endpoint
        $orderId = isset($data['orderId']) ? (int) $data['orderId'] : 0;

        switch ($method) {
            case 'GET':
                // Filter transactions by order ID
                $transactions = array_filter($this->transactions, function ($transaction) use ($orderId) {
                    return $transaction['order_id'] === $orderId;
                });

                // Check if there are any transactions for this order
                if (empty($transactions)) {
                    return [
                        'status' => 'success',
                        'code' => 200,
                        'data' => []
                    ];
                }

                // Check if the user is authorized to access these transactions
                $transaction = reset($transactions);
                if (!$this->isAdmin($headers) && !$this->isCurrentUser($headers, $transaction['user_id'])) {
                    return [
                        'status' => 'error',
                        'code' => 403,
                        'message' => 'Forbidden'
                    ];
                }

                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => array_values($transactions)
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
     * Handle requests to the /process-payment endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleProcessPayment(string $method, array $data, array $headers): array
    {
        switch ($method) {
            case 'POST':
                // Validate required fields
                if (empty($data['user_id']) || empty($data['order_id']) || empty($data['amount']) || empty($data['payment_method_id'])) {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Missing required fields'
                    ];
                }

                // Check if payment method exists
                $paymentMethodId = (int) $data['payment_method_id'];
                if (!isset($this->paymentMethods[$paymentMethodId])) {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Invalid payment method'
                    ];
                }

                // Check if payment method belongs to the user
                $userId = (int) $data['user_id'];
                if ($this->paymentMethods[$paymentMethodId]['user_id'] !== $userId) {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Payment method does not belong to the user'
                    ];
                }

                // Check if there's already a completed payment for this order
                $orderId = (int) $data['order_id'];
                foreach ($this->transactions as $transaction) {
                    if ($transaction['order_id'] === $orderId && $transaction['status'] === 'completed') {
                        return [
                            'status' => 'error',
                            'code' => 400,
                            'message' => 'Order has already been paid'
                        ];
                    }
                }

                // Process payment (in a real implementation, this would call a payment gateway)
                // For this example, we'll simulate a successful payment
                $success = true;
                $errorMessage = '';

                // Create transaction
                $id = count($this->transactions) + 1;
                $transaction = [
                    'id' => $id,
                    'user_id' => $userId,
                    'order_id' => $orderId,
                    'payment_method_id' => $paymentMethodId,
                    'amount' => (float) $data['amount'],
                    'currency' => $data['currency'] ?? 'USD',
                    'status' => $success ? 'completed' : 'failed',
                    'reference' => 'TXN-' . uniqid(),
                    'description' => $data['description'] ?? 'Payment for Order #' . $orderId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->transactions[$id] = $transaction;

                // Publish event based on payment status
                if (isset($data['broker']) && $data['broker'] instanceof Broker) {
                    if ($success) {
                        $this->publishEvent($data['broker'], 'payment-processed', [
                            'transaction_id' => $id,
                            'order_id' => $orderId,
                            'user_id' => $userId,
                            'amount' => $transaction['amount'],
                            'currency' => $transaction['currency'],
                            'status' => $transaction['status']
                        ]);
                    } else {
                        $this->publishEvent($data['broker'], 'payment-failed', [
                            'transaction_id' => $id,
                            'order_id' => $orderId,
                            'user_id' => $userId,
                            'amount' => $transaction['amount'],
                            'currency' => $transaction['currency'],
                            'status' => $transaction['status'],
                            'error_message' => $errorMessage
                        ]);
                    }
                }

                return [
                    'status' => $success ? 'success' : 'error',
                    'code' => $success ? 200 : 400,
                    'data' => $success ? $transaction : ['message' => $errorMessage]
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
     * Handle requests to the /refund endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleRefund(string $method, array $data, array $headers): array
    {
        switch ($method) {
            case 'POST':
                // This endpoint is for admin use only
                if (!$this->isAdmin($headers)) {
                    return [
                        'status' => 'error',
                        'code' => 403,
                        'message' => 'Forbidden'
                    ];
                }

                // Validate required fields
                if (empty($data['transaction_id'])) {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Missing required fields'
                    ];
                }

                // Check if transaction exists
                $transactionId = (int) $data['transaction_id'];
                if (!isset($this->transactions[$transactionId])) {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Invalid transaction'
                    ];
                }

                // Check if transaction is completed
                if ($this->transactions[$transactionId]['status'] !== 'completed') {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Only completed transactions can be refunded'
                    ];
                }

                // Process refund (in a real implementation, this would call a payment gateway)
                // For this example, we'll simulate a successful refund
                $success = true;
                $errorMessage = '';

                // Update transaction status
                $this->transactions[$transactionId]['status'] = 'refunded';
                $this->transactions[$transactionId]['updated_at'] = date('Y-m-d H:i:s');

                // Publish refund event
                if (isset($data['broker']) && $data['broker'] instanceof Broker) {
                    $this->publishEvent($data['broker'], 'payment-refunded', [
                        'transaction_id' => $transactionId,
                        'order_id' => $this->transactions[$transactionId]['order_id'],
                        'user_id' => $this->transactions[$transactionId]['user_id'],
                        'amount' => $this->transactions[$transactionId]['amount'],
                        'currency' => $this->transactions[$transactionId]['currency'],
                        'status' => $this->transactions[$transactionId]['status']
                    ]);
                }

                return [
                    'status' => $success ? 'success' : 'error',
                    'code' => $success ? 200 : 400,
                    'data' => $success ? $this->transactions[$transactionId] : ['message' => $errorMessage]
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
     * Handle requests to the /payment-method-types endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handlePaymentMethodTypes(string $method, array $data, array $headers): array
    {
        switch ($method) {
            case 'GET':
                $types = [];

                foreach ($this->paymentMethodTypes as $key => $value) {
                    $types[] = [
                        'code' => $key,
                        'name' => $value
                    ];
                }

                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $types
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
     * Handle requests to the /payment-statuses endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handlePaymentStatuses(string $method, array $data, array $headers): array
    {
        switch ($method) {
            case 'GET':
                $statuses = [];

                foreach ($this->paymentStatuses as $key => $value) {
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
     * Handle the order-created event
     * 
     * @param array $event The event data
     * @return void
     */
    public function handleOrderCreatedEvent(array $event): void
    {
        // In a real implementation, this might initiate a payment request
        // For this example, we'll just log the event
        error_log("Order created with ID {$event['message']['order_id']} and total {$event['message']['total']}");
    }

    /**
     * Handle the order-cancelled event
     * 
     * @param array $event The event data
     * @return void
     */
    public function handleOrderCancelledEvent(array $event): void
    {
        // In a real implementation, this might cancel a pending payment
        // For this example, we'll just log the event
        error_log("Order cancelled with ID {$event['message']['order_id']}");

        // Check if there's a pending payment for this order
        $orderId = $event['message']['order_id'];
        foreach ($this->transactions as $key => $transaction) {
            if ($transaction['order_id'] === $orderId && $transaction['status'] === 'pending') {
                // Cancel the payment
                $this->transactions[$key]['status'] = 'failed';
                $this->transactions[$key]['updated_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
    }

    /**
     * Check if the current user is an admin
     * 
     * @param array $headers The request headers
     * @return bool True if the user is an admin, false otherwise
     */
    private function isAdmin(array $headers): bool
    {
        // In a real implementation, this would verify the JWT token in the Authorization header
        // For this example, we'll just check if the X-User-Role header is set to 'admin'
        return isset($headers['X-User-Role']) && $headers['X-User-Role'] === 'admin';
    }

    /**
     * Check if the current user is the specified user
     * 
     * @param array $headers The request headers
     * @param int $userId The user ID to check
     * @return bool True if the current user is the specified user, false otherwise
     */
    private function isCurrentUser(array $headers, int $userId): bool
    {
        // In a real implementation, this would verify the JWT token in the Authorization header
        // For this example, we'll just check if the X-User-ID header matches the specified user ID
        return isset($headers['X-User-ID']) && (int) $headers['X-User-ID'] === $userId;
    }
}

<?php

namespace ServerlessArchitecture\Functions;

/**
 * ProductFunction handles product-related operations in a serverless environment.
 * In a serverless architecture, functions are small, single-purpose pieces of code
 * that are triggered by events and run in a stateless environment.
 */
class ProductFunction
{
    /**
     * Create a new product
     *
     * @param array $event The event data containing product information
     * @param object $context The runtime information of the Lambda function
     * @return array The response containing the result of the operation
     */
    public function createProduct(array $event, object $context): array
    {
        // Extract product data from the event
        $productData = $event['body'] ?? [];
        
        // Validate product data
        if (empty($productData['name']) || empty($productData['price'])) {
            return [
                'statusCode' => 400,
                'body' => json_encode([
                    'message' => 'Name and price are required'
                ])
            ];
        }
        
        // In a real application, we would save the product to a database
        // For this example, we'll just return a success response
        return [
            'statusCode' => 201,
            'body' => json_encode([
                'message' => 'Product created successfully',
                'product' => [
                    'id' => uniqid(),
                    'name' => $productData['name'],
                    'price' => $productData['price'],
                    'description' => $productData['description'] ?? '',
                    'createdAt' => date('Y-m-d H:i:s')
                ]
            ])
        ];
    }
    
    /**
     * Get a product by ID
     *
     * @param array $event The event data containing the product ID
     * @param object $context The runtime information of the Lambda function
     * @return array The response containing the result of the operation
     */
    public function getProduct(array $event, object $context): array
    {
        // Extract product ID from the event
        $productId = $event['pathParameters']['productId'] ?? null;
        
        // Validate product ID
        if (empty($productId)) {
            return [
                'statusCode' => 400,
                'body' => json_encode([
                    'message' => 'Product ID is required'
                ])
            ];
        }
        
        // In a real application, we would fetch the product from a database
        // For this example, we'll just return a mock product
        return [
            'statusCode' => 200,
            'body' => json_encode([
                'product' => [
                    'id' => $productId,
                    'name' => 'Sample Product',
                    'price' => 99.99,
                    'description' => 'This is a sample product',
                    'createdAt' => date('Y-m-d H:i:s')
                ]
            ])
        ];
    }
    
    /**
     * List all products
     *
     * @param array $event The event data
     * @param object $context The runtime information of the Lambda function
     * @return array The response containing the result of the operation
     */
    public function listProducts(array $event, object $context): array
    {
        // In a real application, we would fetch products from a database
        // For this example, we'll just return mock products
        return [
            'statusCode' => 200,
            'body' => json_encode([
                'products' => [
                    [
                        'id' => '1',
                        'name' => 'Product 1',
                        'price' => 19.99,
                        'description' => 'Description for product 1'
                    ],
                    [
                        'id' => '2',
                        'name' => 'Product 2',
                        'price' => 29.99,
                        'description' => 'Description for product 2'
                    ],
                    [
                        'id' => '3',
                        'name' => 'Product 3',
                        'price' => 39.99,
                        'description' => 'Description for product 3'
                    ]
                ]
            ])
        ];
    }
}
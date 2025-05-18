<?php

namespace ServerlessArchitecture\Functions;

/**
 * UserFunction handles user-related operations in a serverless environment.
 * In a serverless architecture, functions are small, single-purpose pieces of code
 * that are triggered by events and run in a stateless environment.
 */
class UserFunction
{
    /**
     * Create a new user
     *
     * @param array $event The event data containing user information
     * @param object $context The runtime information of the Lambda function
     * @return array The response containing the result of the operation
     */
    public function createUser(array $event, object $context): array
    {
        // Extract user data from the event
        $userData = $event['body'] ?? [];
        
        // Validate user data
        if (empty($userData['name']) || empty($userData['email'])) {
            return [
                'statusCode' => 400,
                'body' => json_encode([
                    'message' => 'Name and email are required'
                ])
            ];
        }
        
        // In a real application, we would save the user to a database
        // For this example, we'll just return a success response
        return [
            'statusCode' => 201,
            'body' => json_encode([
                'message' => 'User created successfully',
                'user' => [
                    'id' => uniqid(),
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'createdAt' => date('Y-m-d H:i:s')
                ]
            ])
        ];
    }
    
    /**
     * Get a user by ID
     *
     * @param array $event The event data containing the user ID
     * @param object $context The runtime information of the Lambda function
     * @return array The response containing the result of the operation
     */
    public function getUser(array $event, object $context): array
    {
        // Extract user ID from the event
        $userId = $event['pathParameters']['userId'] ?? null;
        
        // Validate user ID
        if (empty($userId)) {
            return [
                'statusCode' => 400,
                'body' => json_encode([
                    'message' => 'User ID is required'
                ])
            ];
        }
        
        // In a real application, we would fetch the user from a database
        // For this example, we'll just return a mock user
        return [
            'statusCode' => 200,
            'body' => json_encode([
                'user' => [
                    'id' => $userId,
                    'name' => 'John Doe',
                    'email' => 'john.doe@example.com',
                    'createdAt' => date('Y-m-d H:i:s')
                ]
            ])
        ];
    }
    
    /**
     * Delete a user by ID
     *
     * @param array $event The event data containing the user ID
     * @param object $context The runtime information of the Lambda function
     * @return array The response containing the result of the operation
     */
    public function deleteUser(array $event, object $context): array
    {
        // Extract user ID from the event
        $userId = $event['pathParameters']['userId'] ?? null;
        
        // Validate user ID
        if (empty($userId)) {
            return [
                'statusCode' => 400,
                'body' => json_encode([
                    'message' => 'User ID is required'
                ])
            ];
        }
        
        // In a real application, we would delete the user from a database
        // For this example, we'll just return a success response
        return [
            'statusCode' => 200,
            'body' => json_encode([
                'message' => 'User deleted successfully'
            ])
        ];
    }
}
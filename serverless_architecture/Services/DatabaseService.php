<?php

namespace ServerlessArchitecture\Services;

/**
 * DatabaseService handles database operations in a serverless environment.
 * In a serverless architecture, managed database services like DynamoDB or Aurora Serverless
 * are used for storing and retrieving data.
 */
class DatabaseService
{
    /**
     * @var array The configuration for the database service
     */
    private $config;
    
    /**
     * @var array In-memory database for this example
     */
    private $database = [];
    
    /**
     * Constructor
     *
     * @param array $config The configuration for the database service
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'tableName' => 'serverless-example-table',
            'region' => 'us-east-1',
            'endpoint' => null
        ], $config);
        
        // Initialize the database with empty tables
        $this->database[$this->config['tableName']] = [];
    }
    
    /**
     * Put an item in the database
     *
     * @param array $item The item to put in the database
     * @return array The result of the put operation
     */
    public function putItem(array $item): array
    {
        // Check if the item has an ID
        if (!isset($item['id'])) {
            $item['id'] = uniqid();
        }
        
        // Add timestamps
        $item['createdAt'] = $item['createdAt'] ?? date('Y-m-d H:i:s');
        $item['updatedAt'] = date('Y-m-d H:i:s');
        
        // Store the item
        $this->database[$this->config['tableName']][$item['id']] = $item;
        
        return [
            'success' => true,
            'message' => 'Item put successfully',
            'data' => [
                'tableName' => $this->config['tableName'],
                'item' => $item
            ]
        ];
    }
    
    /**
     * Get an item from the database
     *
     * @param string $id The ID of the item to get
     * @return array The result of the get operation
     */
    public function getItem(string $id): array
    {
        // Check if the item exists
        if (!isset($this->database[$this->config['tableName']][$id])) {
            return [
                'success' => false,
                'message' => 'Item not found'
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Item retrieved successfully',
            'data' => [
                'tableName' => $this->config['tableName'],
                'item' => $this->database[$this->config['tableName']][$id]
            ]
        ];
    }
    
    /**
     * Delete an item from the database
     *
     * @param string $id The ID of the item to delete
     * @return array The result of the delete operation
     */
    public function deleteItem(string $id): array
    {
        // Check if the item exists
        if (!isset($this->database[$this->config['tableName']][$id])) {
            return [
                'success' => false,
                'message' => 'Item not found'
            ];
        }
        
        // Delete the item
        unset($this->database[$this->config['tableName']][$id]);
        
        return [
            'success' => true,
            'message' => 'Item deleted successfully',
            'data' => [
                'tableName' => $this->config['tableName'],
                'id' => $id
            ]
        ];
    }
    
    /**
     * Query items from the database
     *
     * @param array $query The query parameters
     * @return array The result of the query operation
     */
    public function queryItems(array $query = []): array
    {
        $items = [];
        
        // Simple implementation for this example
        // In a real application, we would use a more sophisticated query mechanism
        foreach ($this->database[$this->config['tableName']] as $id => $item) {
            $match = true;
            
            foreach ($query as $key => $value) {
                if (!isset($item[$key]) || $item[$key] !== $value) {
                    $match = false;
                    break;
                }
            }
            
            if ($match) {
                $items[] = $item;
            }
        }
        
        return [
            'success' => true,
            'message' => 'Items queried successfully',
            'data' => [
                'tableName' => $this->config['tableName'],
                'query' => $query,
                'items' => $items,
                'count' => count($items)
            ]
        ];
    }
    
    /**
     * Scan all items in the database
     *
     * @return array The result of the scan operation
     */
    public function scanItems(): array
    {
        return [
            'success' => true,
            'message' => 'Items scanned successfully',
            'data' => [
                'tableName' => $this->config['tableName'],
                'items' => array_values($this->database[$this->config['tableName']]),
                'count' => count($this->database[$this->config['tableName']])
            ]
        ];
    }
    
    /**
     * Get the configuration for the database service
     *
     * @return array The configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
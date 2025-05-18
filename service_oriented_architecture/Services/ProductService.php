<?php

namespace ServiceOrientedArchitecture\Services;

use ServiceOrientedArchitecture\Contracts\ProductServiceInterface;

/**
 * ProductService implements the ProductServiceInterface.
 * In a Service-Oriented Architecture, services are independent units of functionality
 * that are accessible over a network.
 */
class ProductService implements ProductServiceInterface
{
    /**
     * @var array In-memory product storage for this example
     */
    private $products = [];
    
    /**
     * Constructor
     */
    public function __construct()
    {
        // Initialize with some sample products
        $this->products = [
            '1' => [
                'id' => '1',
                'name' => 'Laptop',
                'price' => 999.99,
                'description' => 'High-performance laptop with 16GB RAM',
                'created_at' => '2023-01-01 00:00:00'
            ],
            '2' => [
                'id' => '2',
                'name' => 'Smartphone',
                'price' => 699.99,
                'description' => 'Latest smartphone with 128GB storage',
                'created_at' => '2023-01-02 00:00:00'
            ],
            '3' => [
                'id' => '3',
                'name' => 'Headphones',
                'price' => 199.99,
                'description' => 'Noise-cancelling wireless headphones',
                'created_at' => '2023-01-03 00:00:00'
            ]
        ];
    }
    
    /**
     * Create a new product
     *
     * @param array $productData The product data
     * @return array The result of the operation
     */
    public function createProduct(array $productData): array
    {
        // Validate product data
        if (empty($productData['name']) || !isset($productData['price'])) {
            return [
                'success' => false,
                'message' => 'Name and price are required'
            ];
        }
        
        // Generate a new product ID
        $productId = (string) (count($this->products) + 1);
        
        // Create the product
        $this->products[$productId] = [
            'id' => $productId,
            'name' => $productData['name'],
            'price' => (float) $productData['price'],
            'description' => $productData['description'] ?? '',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return [
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $this->products[$productId]
        ];
    }
    
    /**
     * Get a product by ID
     *
     * @param string $productId The product ID
     * @return array The result of the operation
     */
    public function getProduct(string $productId): array
    {
        // Check if product exists
        if (!isset($this->products[$productId])) {
            return [
                'success' => false,
                'message' => 'Product not found'
            ];
        }
        
        return [
            'success' => true,
            'data' => $this->products[$productId]
        ];
    }
    
    /**
     * Update a product
     *
     * @param string $productId The product ID
     * @param array $productData The product data to update
     * @return array The result of the operation
     */
    public function updateProduct(string $productId, array $productData): array
    {
        // Check if product exists
        if (!isset($this->products[$productId])) {
            return [
                'success' => false,
                'message' => 'Product not found'
            ];
        }
        
        // Update product data
        if (isset($productData['name'])) {
            $this->products[$productId]['name'] = $productData['name'];
        }
        
        if (isset($productData['price'])) {
            $this->products[$productId]['price'] = (float) $productData['price'];
        }
        
        if (isset($productData['description'])) {
            $this->products[$productId]['description'] = $productData['description'];
        }
        
        return [
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $this->products[$productId]
        ];
    }
    
    /**
     * Delete a product
     *
     * @param string $productId The product ID
     * @return array The result of the operation
     */
    public function deleteProduct(string $productId): array
    {
        // Check if product exists
        if (!isset($this->products[$productId])) {
            return [
                'success' => false,
                'message' => 'Product not found'
            ];
        }
        
        // Delete the product
        $product = $this->products[$productId];
        unset($this->products[$productId]);
        
        return [
            'success' => true,
            'message' => 'Product deleted successfully',
            'data' => $product
        ];
    }
    
    /**
     * List all products
     *
     * @param array $filters Optional filters
     * @return array The result of the operation
     */
    public function listProducts(array $filters = []): array
    {
        $products = $this->products;
        
        // Apply filters if any
        if (!empty($filters)) {
            $products = array_filter($products, function ($product) use ($filters) {
                foreach ($filters as $key => $value) {
                    if (!isset($product[$key])) {
                        return false;
                    }
                    
                    // Handle numeric comparisons
                    if (is_numeric($product[$key]) && is_array($value)) {
                        if (isset($value['min']) && $product[$key] < $value['min']) {
                            return false;
                        }
                        if (isset($value['max']) && $product[$key] > $value['max']) {
                            return false;
                        }
                    } elseif ($product[$key] !== $value) {
                        return false;
                    }
                }
                return true;
            });
        }
        
        return [
            'success' => true,
            'data' => array_values($products)
        ];
    }
    
    /**
     * Search for products
     *
     * @param string $query The search query
     * @param array $options Search options
     * @return array The result of the operation
     */
    public function searchProducts(string $query, array $options = []): array
    {
        if (empty($query)) {
            return [
                'success' => false,
                'message' => 'Search query is required'
            ];
        }
        
        $results = [];
        $query = strtolower($query);
        
        foreach ($this->products as $product) {
            // Search in name and description
            if (strpos(strtolower($product['name']), $query) !== false || 
                strpos(strtolower($product['description']), $query) !== false) {
                $results[] = $product;
            }
        }
        
        // Sort results if specified
        if (isset($options['sort_by']) && isset($options['sort_direction'])) {
            usort($results, function ($a, $b) use ($options) {
                $field = $options['sort_by'];
                $direction = strtolower($options['sort_direction']) === 'desc' ? -1 : 1;
                
                if (!isset($a[$field]) || !isset($b[$field])) {
                    return 0;
                }
                
                if ($a[$field] == $b[$field]) {
                    return 0;
                }
                
                return ($a[$field] < $b[$field] ? -1 : 1) * $direction;
            });
        }
        
        // Limit results if specified
        if (isset($options['limit']) && is_numeric($options['limit'])) {
            $results = array_slice($results, 0, (int) $options['limit']);
        }
        
        return [
            'success' => true,
            'data' => $results,
            'count' => count($results)
        ];
    }
}
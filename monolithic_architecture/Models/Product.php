<?php

namespace MonolithicArchitecture\Models;

/**
 * Product model represents product data and database interactions
 */
class Product
{
    /**
     * Get a product by ID
     * 
     * @param int $productId Product ID
     * @return array|null Product data or null if not found
     */
    public function getById(int $productId): ?array
    {
        // In a real application, we would fetch the product from the database
        // For this example, we'll simulate some products
        $products = $this->getAll();
        
        foreach ($products as $product) {
            if ($product['id'] === $productId) {
                return $product;
            }
        }

        return null;
    }

    /**
     * Create a new product
     * 
     * @param array $data Product data
     * @return array Created product data
     */
    public function create(array $data): array
    {
        // In a real application, we would save the product to the database
        // For this example, we'll simulate creating a product
        return [
            'id' => 4, // Simulate a new ID
            'name' => $data['name'],
            'price' => $data['price'],
            'description' => $data['description'] ?? '',
            'created_at' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Update a product
     * 
     * @param int $productId Product ID
     * @param array $data Product data to update
     * @return array Updated product data
     */
    public function update(int $productId, array $data): array
    {
        // In a real application, we would update the product in the database
        // For this example, we'll simulate an update
        return [
            'id' => $productId,
            'name' => $data['name'],
            'price' => $data['price'],
            'description' => $data['description'],
            'created_at' => $data['created_at'],
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Delete a product
     * 
     * @param int $productId Product ID
     * @return bool True if deleted, false otherwise
     */
    public function delete(int $productId): bool
    {
        // In a real application, we would delete the product from the database
        // For this example, we'll simulate a deletion
        return true;
    }

    /**
     * Get all products
     * 
     * @return array List of products
     */
    public function getAll(): array
    {
        // In a real application, we would fetch all products from the database
        // For this example, we'll simulate some products
        return [
            [
                'id' => 1,
                'name' => 'Laptop',
                'price' => 999.99,
                'description' => 'High-performance laptop with 16GB RAM and 512GB SSD',
                'created_at' => '2023-01-01 00:00:00'
            ],
            [
                'id' => 2,
                'name' => 'Smartphone',
                'price' => 699.99,
                'description' => 'Latest smartphone with 128GB storage and 5G connectivity',
                'created_at' => '2023-01-02 00:00:00'
            ],
            [
                'id' => 3,
                'name' => 'Headphones',
                'price' => 149.99,
                'description' => 'Wireless noise-cancelling headphones with 30-hour battery life',
                'created_at' => '2023-01-03 00:00:00'
            ]
        ];
    }

    /**
     * Search for products
     * 
     * @param string $query Search query
     * @return array List of matching products
     */
    public function search(string $query): array
    {
        // In a real application, we would search the database
        // For this example, we'll simulate a search
        $query = strtolower($query);
        $results = [];
        
        foreach ($this->getAll() as $product) {
            if (strpos(strtolower($product['name']), $query) !== false || 
                strpos(strtolower($product['description']), $query) !== false) {
                $results[] = $product;
            }
        }
        
        return $results;
    }

    /**
     * Get products by category
     * 
     * @param string $category Category name
     * @return array List of products in the category
     */
    public function getByCategory(string $category): array
    {
        // In a real application, we would fetch products by category from the database
        // For this example, we'll simulate some categorized products
        $products = $this->getAll();
        
        // Simulate categories
        $categories = [
            'electronics' => [1, 2, 3],
            'audio' => [3],
            'computers' => [1],
            'phones' => [2]
        ];
        
        if (!isset($categories[$category])) {
            return [];
        }
        
        $categoryProductIds = $categories[$category];
        $results = [];
        
        foreach ($products as $product) {
            if (in_array($product['id'], $categoryProductIds)) {
                $results[] = $product;
            }
        }
        
        return $results;
    }
}
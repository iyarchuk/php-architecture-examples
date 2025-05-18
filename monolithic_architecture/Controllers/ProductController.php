<?php

namespace MonolithicArchitecture\Controllers;

use MonolithicArchitecture\Services\ProductService;

/**
 * ProductController handles product-related HTTP requests
 */
class ProductController
{
    private $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    /**
     * Get all products
     * 
     * @param array $headers Request headers
     * @return array Response with products data
     */
    public function getProducts(array $headers): array
    {
        // Validate authentication
        if (!$this->isAuthenticated($headers)) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized'
            ];
        }

        // Call the service to get all products
        return $this->productService->getAllProducts();
    }

    /**
     * Get a specific product
     * 
     * @param int $productId Product ID
     * @param array $headers Request headers
     * @return array Response with product data
     */
    public function getProduct(int $productId, array $headers): array
    {
        // Validate authentication
        if (!$this->isAuthenticated($headers)) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized'
            ];
        }

        // Call the service to get the product
        return $this->productService->getProduct($productId);
    }

    /**
     * Create a new product
     * 
     * @param array $data Product data
     * @param array $headers Request headers
     * @return array Response with created product data
     */
    public function createProduct(array $data, array $headers): array
    {
        // Validate authentication
        if (!$this->isAuthenticated($headers)) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized'
            ];
        }

        // Validate input
        if (!isset($data['name']) || !isset($data['price'])) {
            return [
                'status' => 'error',
                'message' => 'Name and price are required'
            ];
        }

        // Call the service to create the product
        return $this->productService->createProduct($data);
    }

    /**
     * Update a product
     * 
     * @param int $productId Product ID
     * @param array $data Product data to update
     * @param array $headers Request headers
     * @return array Response with updated product data
     */
    public function updateProduct(int $productId, array $data, array $headers): array
    {
        // Validate authentication
        if (!$this->isAuthenticated($headers)) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized'
            ];
        }

        // Call the service to update the product
        return $this->productService->updateProduct($productId, $data);
    }

    /**
     * Delete a product
     * 
     * @param int $productId Product ID
     * @param array $headers Request headers
     * @return array Response with deletion status
     */
    public function deleteProduct(int $productId, array $headers): array
    {
        // Validate authentication
        if (!$this->isAuthenticated($headers)) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized'
            ];
        }

        // Call the service to delete the product
        return $this->productService->deleteProduct($productId);
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
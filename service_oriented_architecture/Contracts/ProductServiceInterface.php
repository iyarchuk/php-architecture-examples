<?php

namespace ServiceOrientedArchitecture\Contracts;

/**
 * ProductServiceInterface defines the contract for the Product Service.
 * In a Service-Oriented Architecture, service contracts define the operations
 * that services can perform and the data they exchange.
 */
interface ProductServiceInterface
{
    /**
     * Create a new product
     *
     * @param array $productData The product data
     * @return array The result of the operation
     */
    public function createProduct(array $productData): array;
    
    /**
     * Get a product by ID
     *
     * @param string $productId The product ID
     * @return array The result of the operation
     */
    public function getProduct(string $productId): array;
    
    /**
     * Update a product
     *
     * @param string $productId The product ID
     * @param array $productData The product data to update
     * @return array The result of the operation
     */
    public function updateProduct(string $productId, array $productData): array;
    
    /**
     * Delete a product
     *
     * @param string $productId The product ID
     * @return array The result of the operation
     */
    public function deleteProduct(string $productId): array;
    
    /**
     * List all products
     *
     * @param array $filters Optional filters
     * @return array The result of the operation
     */
    public function listProducts(array $filters = []): array;
    
    /**
     * Search for products
     *
     * @param string $query The search query
     * @param array $options Search options
     * @return array The result of the operation
     */
    public function searchProducts(string $query, array $options = []): array;
}
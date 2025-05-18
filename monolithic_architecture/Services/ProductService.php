<?php

namespace MonolithicArchitecture\Services;

use MonolithicArchitecture\Models\Product;
use MonolithicArchitecture\Utils\Logger;
use MonolithicArchitecture\Utils\Validator;

/**
 * ProductService contains business logic for product-related operations
 */
class ProductService
{
    private $productModel;
    private $logger;
    private $validator;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->logger = new Logger();
        $this->validator = new Validator();
    }

    /**
     * Get all products
     * 
     * @return array Response with products data
     */
    public function getAllProducts(): array
    {
        $this->logger->info("Getting all products");

        // In a real application, we would fetch products from the database
        // For this example, we'll simulate some products
        $products = $this->productModel->getAll();

        $this->logger->info("Retrieved " . count($products) . " products");

        return [
            'status' => 'success',
            'data' => $products
        ];
    }

    /**
     * Get a specific product
     * 
     * @param int $productId Product ID
     * @return array Response with product data
     */
    public function getProduct(int $productId): array
    {
        $this->logger->info("Getting product: $productId");

        // In a real application, we would fetch the product from the database
        // For this example, we'll simulate a product
        $product = $this->productModel->getById($productId);

        if (!$product) {
            $this->logger->warning("Product not found: $productId");
            return [
                'status' => 'error',
                'message' => 'Product not found'
            ];
        }

        $this->logger->info("Retrieved product: $productId");

        return [
            'status' => 'success',
            'data' => $product
        ];
    }

    /**
     * Create a new product
     * 
     * @param array $data Product data
     * @return array Response with created product data
     */
    public function createProduct(array $data): array
    {
        $this->logger->info("Creating new product: {$data['name']}");

        // Validate product data
        if (!$this->validator->isValidProductData($data)) {
            $this->logger->warning("Invalid product data");
            return [
                'status' => 'error',
                'message' => 'Invalid product data'
            ];
        }

        // In a real application, we would save the product to the database
        // For this example, we'll simulate creating a product
        $product = $this->productModel->create($data);

        $this->logger->info("Product created: {$product['id']}");

        return [
            'status' => 'success',
            'data' => $product
        ];
    }

    /**
     * Update a product
     * 
     * @param int $productId Product ID
     * @param array $data Product data to update
     * @return array Response with updated product data
     */
    public function updateProduct(int $productId, array $data): array
    {
        $this->logger->info("Updating product: $productId");

        // In a real application, we would update the product in the database
        // For this example, we'll simulate an update
        $product = $this->productModel->getById($productId);

        if (!$product) {
            $this->logger->warning("Product not found: $productId");
            return [
                'status' => 'error',
                'message' => 'Product not found'
            ];
        }

        // Update the product data
        foreach ($data as $key => $value) {
            if (isset($product[$key])) {
                $product[$key] = $value;
            }
        }

        // Save the updated product
        $updatedProduct = $this->productModel->update($productId, $product);

        $this->logger->info("Product updated: $productId");

        return [
            'status' => 'success',
            'data' => $updatedProduct
        ];
    }

    /**
     * Delete a product
     * 
     * @param int $productId Product ID
     * @return array Response with deletion status
     */
    public function deleteProduct(int $productId): array
    {
        $this->logger->info("Deleting product: $productId");

        // In a real application, we would delete the product from the database
        // For this example, we'll simulate a deletion
        $product = $this->productModel->getById($productId);

        if (!$product) {
            $this->logger->warning("Product not found: $productId");
            return [
                'status' => 'error',
                'message' => 'Product not found'
            ];
        }

        // Delete the product
        $result = $this->productModel->delete($productId);

        if ($result) {
            $this->logger->info("Product deleted: $productId");
            return [
                'status' => 'success',
                'message' => 'Product deleted successfully'
            ];
        } else {
            $this->logger->error("Failed to delete product: $productId");
            return [
                'status' => 'error',
                'message' => 'Failed to delete product'
            ];
        }
    }

    /**
     * Search for products
     * 
     * @param string $query Search query
     * @return array Response with search results
     */
    public function searchProducts(string $query): array
    {
        $this->logger->info("Searching for products: $query");

        // In a real application, we would search the database
        // For this example, we'll simulate a search
        $products = $this->productModel->search($query);

        $this->logger->info("Found " . count($products) . " products matching: $query");

        return [
            'status' => 'success',
            'data' => $products
        ];
    }
}
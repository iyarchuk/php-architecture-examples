<?php

namespace SpaceBasedArchitecture\ProcessingUnits;

use SpaceBasedArchitecture\Models\Order;
use SpaceBasedArchitecture\Models\InventoryItem;
use SpaceBasedArchitecture\Space\SpaceInterface;

/**
 * Order Processing Unit for the space-based architecture example.
 * Handles order processing, including validation, inventory checks, and order status updates.
 */
class OrderProcessingUnit extends ProcessingUnit
{
    /**
     * Initialize the processing unit.
     *
     * @param SpaceInterface $space The space to use for communication
     * @return void
     */
    public function initialize(SpaceInterface $space): void
    {
        parent::initialize($space);
        
        // Register event listeners
        $this->space->on('write', function ($data) {
            $tuple = $data['tuple'] ?? null;
            if ($tuple instanceof Order && $tuple->getStatus() === 'pending') {
                $this->process($tuple);
            }
        });
    }

    /**
     * Process an order.
     *
     * @param mixed $data The order to process
     * @return mixed The processed order
     */
    public function process($data)
    {
        if (!$data instanceof Order) {
            return null;
        }
        
        $order = $data;
        
        // Validate the order
        if (!$this->validateOrder($order)) {
            $order->setStatus('invalid');
            $this->space->write($order, $order->getId());
            return $order;
        }
        
        // Check inventory
        if (!$this->checkInventory($order)) {
            $order->setStatus('out_of_stock');
            $this->space->write($order, $order->getId());
            return $order;
        }
        
        // Update inventory
        $this->updateInventory($order);
        
        // Process payment (simplified for the example)
        $paymentSuccessful = $this->processPayment($order);
        
        if ($paymentSuccessful) {
            $order->setStatus('completed');
        } else {
            $order->setStatus('payment_failed');
        }
        
        // Write the updated order back to the space
        $this->space->write($order, $order->getId());
        
        return $order;
    }

    /**
     * Start the processing unit.
     *
     * @return void
     */
    public function start(): void
    {
        parent::start();
        
        // Process any pending orders that are already in the space
        $pendingOrders = $this->space->readAll(['status' => 'pending']);
        
        foreach ($pendingOrders as $orderId => $order) {
            if ($order instanceof Order) {
                $this->process($order);
            }
        }
    }

    /**
     * Validate an order.
     *
     * @param Order $order The order to validate
     * @return bool True if the order is valid, false otherwise
     */
    protected function validateOrder(Order $order): bool
    {
        // Check if the order has a customer ID
        if (empty($order->getCustomerId())) {
            return false;
        }
        
        // Check if the order has items
        if (empty($order->getItems())) {
            return false;
        }
        
        // Check if all items have a product ID, price, and quantity
        foreach ($order->getItems() as $item) {
            if (!isset($item['productId']) || !isset($item['price']) || !isset($item['quantity'])) {
                return false;
            }
            
            if ($item['quantity'] <= 0 || $item['price'] < 0) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Check if all items in the order are in stock.
     *
     * @param Order $order The order to check
     * @return bool True if all items are in stock, false otherwise
     */
    protected function checkInventory(Order $order): bool
    {
        foreach ($order->getItems() as $item) {
            $productId = $item['productId'];
            $quantity = $item['quantity'];
            
            // Find the inventory item in the space
            $inventoryItem = $this->space->read(['productId' => $productId]);
            
            if (!$inventoryItem instanceof InventoryItem || !$inventoryItem->isInStock($quantity)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Update the inventory based on the order.
     *
     * @param Order $order The order to update the inventory for
     * @return void
     */
    protected function updateInventory(Order $order): void
    {
        foreach ($order->getItems() as $item) {
            $productId = $item['productId'];
            $quantity = $item['quantity'];
            
            // Find the inventory item in the space
            $inventoryItems = $this->space->readAll(['productId' => $productId]);
            
            foreach ($inventoryItems as $id => $inventoryItem) {
                if ($inventoryItem instanceof InventoryItem) {
                    $inventoryItem->decreaseQuantity($quantity);
                    $this->space->write($inventoryItem, $id);
                }
            }
        }
    }

    /**
     * Process payment for an order.
     *
     * @param Order $order The order to process payment for
     * @return bool True if the payment was successful, false otherwise
     */
    protected function processPayment(Order $order): bool
    {
        // Simplified payment processing for the example
        // In a real-world scenario, this would integrate with a payment gateway
        
        // Simulate a successful payment 90% of the time
        return mt_rand(1, 10) <= 9;
    }
}
<?php

namespace SpaceBasedArchitecture\ProcessingUnits;

use SpaceBasedArchitecture\Models\InventoryItem;
use SpaceBasedArchitecture\Space\SpaceInterface;

/**
 * Inventory Processing Unit for the space-based architecture example.
 * Handles inventory management, including stock updates, low stock alerts, and inventory reporting.
 */
class InventoryProcessingUnit extends ProcessingUnit
{
    /**
     * The threshold for low stock alerts.
     *
     * @var int
     */
    protected $lowStockThreshold = 5;

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
            if ($tuple instanceof InventoryItem) {
                $this->checkLowStock($tuple);
            }
        });
    }

    /**
     * Process an inventory item.
     *
     * @param mixed $data The inventory item to process
     * @return mixed The processed inventory item
     */
    public function process($data)
    {
        if (!$data instanceof InventoryItem) {
            return null;
        }
        
        $inventoryItem = $data;
        
        // Check for low stock
        $this->checkLowStock($inventoryItem);
        
        return $inventoryItem;
    }

    /**
     * Start the processing unit.
     *
     * @return void
     */
    public function start(): void
    {
        parent::start();
        
        // Process all inventory items that are already in the space
        $inventoryItems = $this->space->readAll();
        
        foreach ($inventoryItems as $id => $item) {
            if ($item instanceof InventoryItem) {
                $this->process($item);
            }
        }
    }

    /**
     * Check if an inventory item is low on stock and generate an alert if needed.
     *
     * @param InventoryItem $item The inventory item to check
     * @return void
     */
    protected function checkLowStock(InventoryItem $item): void
    {
        if ($item->getQuantity() <= $this->lowStockThreshold) {
            $this->generateLowStockAlert($item);
        }
    }

    /**
     * Generate a low stock alert for an inventory item.
     *
     * @param InventoryItem $item The inventory item to generate an alert for
     * @return void
     */
    protected function generateLowStockAlert(InventoryItem $item): void
    {
        // In a real-world scenario, this would send an alert to the appropriate channels
        // For this example, we'll just log the alert
        
        $alert = [
            'type' => 'low_stock_alert',
            'productId' => $item->getProductId(),
            'name' => $item->getName(),
            'quantity' => $item->getQuantity(),
            'threshold' => $this->lowStockThreshold,
            'timestamp' => time(),
        ];
        
        // Write the alert to the space
        $this->space->write($alert);
        
        // Log the alert (simplified for the example)
        echo "Low stock alert: {$item->getName()} (ID: {$item->getProductId()}) has only {$item->getQuantity()} items left.\n";
    }

    /**
     * Set the low stock threshold.
     *
     * @param int $threshold The threshold
     * @return void
     */
    public function setLowStockThreshold(int $threshold): void
    {
        $this->lowStockThreshold = $threshold;
    }

    /**
     * Get the low stock threshold.
     *
     * @return int The threshold
     */
    public function getLowStockThreshold(): int
    {
        return $this->lowStockThreshold;
    }

    /**
     * Generate an inventory report.
     *
     * @return array The inventory report
     */
    public function generateInventoryReport(): array
    {
        $inventoryItems = $this->space->readAll();
        $report = [
            'totalItems' => 0,
            'totalValue' => 0.0,
            'lowStockItems' => [],
            'items' => [],
        ];
        
        foreach ($inventoryItems as $id => $item) {
            if ($item instanceof InventoryItem) {
                $itemData = $item->toArray();
                $itemValue = $item->getPrice() * $item->getQuantity();
                
                $report['items'][] = $itemData;
                $report['totalItems'] += $item->getQuantity();
                $report['totalValue'] += $itemValue;
                
                if ($item->getQuantity() <= $this->lowStockThreshold) {
                    $report['lowStockItems'][] = $itemData;
                }
            }
        }
        
        return $report;
    }

    /**
     * Restock an inventory item.
     *
     * @param string $productId The product ID
     * @param int $quantity The quantity to add
     * @return bool True if the item was restocked, false otherwise
     */
    public function restockItem(string $productId, int $quantity): bool
    {
        if ($quantity <= 0) {
            return false;
        }
        
        $inventoryItems = $this->space->readAll(['productId' => $productId]);
        
        foreach ($inventoryItems as $id => $item) {
            if ($item instanceof InventoryItem) {
                $item->increaseQuantity($quantity);
                $this->space->write($item, $id);
                return true;
            }
        }
        
        return false;
    }
}
<?php

// Autoload classes (in a real application, you would use Composer's autoloader)
spl_autoload_register(function ($class) {
    $prefix = 'SpaceBasedArchitecture\\';
    $base_dir = __DIR__ . '/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Import classes
use SpaceBasedArchitecture\Space\Space;
use SpaceBasedArchitecture\ProcessingUnits\OrderProcessingUnit;
use SpaceBasedArchitecture\ProcessingUnits\InventoryProcessingUnit;
use SpaceBasedArchitecture\Middleware\SpaceBasedMiddleware;
use SpaceBasedArchitecture\EventProcessing\EventProcessor;
use SpaceBasedArchitecture\Models\Order;
use SpaceBasedArchitecture\Models\InventoryItem;

// Example 1: Setting up the Space and Space-Based Middleware
echo "Example 1: Setting up the Space and Space-Based Middleware\n";

// Create a space
$space = new Space();

// Create a middleware
$middleware = new SpaceBasedMiddleware();
$middleware->initialize($space);

// Create processing units
$orderProcessingUnit = new OrderProcessingUnit('order-processor');
$inventoryProcessingUnit = new InventoryProcessingUnit('inventory-processor');

// Register processing units with the middleware
$middleware->registerProcessingUnit($orderProcessingUnit);
$middleware->registerProcessingUnit($inventoryProcessingUnit);

// Start the processing units
$middleware->startProcessingUnits();

echo "Middleware status: {$middleware->getStatus()}\n";
echo "Order Processing Unit status: {$orderProcessingUnit->getStatus()}\n";
echo "Inventory Processing Unit status: {$inventoryProcessingUnit->getStatus()}\n";
echo "\n";

// Example 2: Creating and Using an Event Processor
echo "Example 2: Creating and Using an Event Processor\n";

// Create an event processor
$eventProcessor = new EventProcessor();
$eventProcessor->initialize($space);

// Register event handlers
$eventProcessor->registerHandler('write', function ($data) {
    $tuple = $data['tuple'] ?? null;
    if ($tuple instanceof Order) {
        echo "Event: Order written to space (ID: {$tuple->getId()}, Status: {$tuple->getStatus()})\n";
    } elseif ($tuple instanceof InventoryItem) {
        echo "Event: Inventory item written to space (ID: {$tuple->getId()}, Product: {$tuple->getName()}, Quantity: {$tuple->getQuantity()})\n";
    }
});

$eventProcessor->registerHandler('take', function ($data) {
    $tuple = $data['tuple'] ?? null;
    if ($tuple instanceof Order) {
        echo "Event: Order taken from space (ID: {$tuple->getId()}, Status: {$tuple->getStatus()})\n";
    } elseif ($tuple instanceof InventoryItem) {
        echo "Event: Inventory item taken from space (ID: {$tuple->getId()}, Product: {$tuple->getName()}, Quantity: {$tuple->getQuantity()})\n";
    }
});

// Start the event processor
$eventProcessor->start();

echo "Event Processor status: {$eventProcessor->getStatus()}\n";
echo "\n";

// Example 3: Writing Data to the Space
echo "Example 3: Writing Data to the Space\n";

// Create inventory items
$laptop = new InventoryItem('prod-1', 'Laptop', 999.99, 10, 'High-performance laptop');
$smartphone = new InventoryItem('prod-2', 'Smartphone', 499.99, 20, 'Latest smartphone model');
$tablet = new InventoryItem('prod-3', 'Tablet', 299.99, 15, 'Lightweight tablet');
$headphones = new InventoryItem('prod-4', 'Headphones', 99.99, 30, 'Noise-cancelling headphones');
$keyboard = new InventoryItem('prod-5', 'Keyboard', 49.99, 3, 'Mechanical keyboard'); // Low stock

// Write inventory items to the space
$space->write($laptop);
$space->write($smartphone);
$space->write($tablet);
$space->write($headphones);
$space->write($keyboard);

echo "Inventory items written to space\n";
echo "\n";

// Example 4: Reading and Taking Data from the Space
echo "Example 4: Reading and Taking Data from the Space\n";

// Read an inventory item from the space
$laptopFromSpace = $space->read(['productId' => 'prod-1']);
if ($laptopFromSpace instanceof InventoryItem) {
    echo "Read laptop from space: {$laptopFromSpace->getName()} (Quantity: {$laptopFromSpace->getQuantity()})\n";
}

// Take an inventory item from the space
$headphonesFromSpace = $space->take(['productId' => 'prod-4']);
if ($headphonesFromSpace instanceof InventoryItem) {
    echo "Took headphones from space: {$headphonesFromSpace->getName()} (Quantity: {$headphonesFromSpace->getQuantity()})\n";
}

// Check if an item exists in the space
$tabletExists = $space->exists(['productId' => 'prod-3']);
echo "Tablet exists in space: " . ($tabletExists ? 'Yes' : 'No') . "\n";

$headphonesExist = $space->exists(['productId' => 'prod-4']);
echo "Headphones exist in space: " . ($headphonesExist ? 'Yes' : 'No') . "\n";

echo "\n";

// Example 5: Processing Orders
echo "Example 5: Processing Orders\n";

// Create an order
$order = new Order('cust-1', [
    [
        'productId' => 'prod-1',
        'name' => 'Laptop',
        'price' => 999.99,
        'quantity' => 1
    ],
    [
        'productId' => 'prod-2',
        'name' => 'Smartphone',
        'price' => 499.99,
        'quantity' => 2
    ]
]);

// Write the order to the space
$space->write($order);

echo "Order written to space (ID: {$order->getId()}, Status: {$order->getStatus()})\n";

// The order processing unit should automatically process the order
// Wait a moment to allow processing to complete
sleep(1);

// Read the processed order from the space
$processedOrder = $space->read(['id' => $order->getId()]);
if ($processedOrder instanceof Order) {
    echo "Processed order from space (ID: {$processedOrder->getId()}, Status: {$processedOrder->getStatus()})\n";
}

echo "\n";

// Example 6: Demonstrating Data Replication and Partitioning
echo "Example 6: Demonstrating Data Replication and Partitioning\n";

// Replicate data across nodes
$middleware->replicateData($laptop);
echo "Laptop data replicated across nodes\n";

// Partition data across nodes
$middleware->partitionData($smartphone, 'prod-2');
echo "Smartphone data partitioned across nodes\n";

// Get node status
$nodeStatus = $middleware->getNodesStatus();
echo "Node status:\n";
foreach ($nodeStatus as $nodeId => $status) {
    echo "- {$nodeId}: {$status}\n";
}

// Simulate a node failure
$middleware->handleNodeFailure('node2');
echo "Node 'node2' failure handled\n";

// Get updated node status
$nodeStatus = $middleware->getNodesStatus();
echo "Updated node status:\n";
foreach ($nodeStatus as $nodeId => $status) {
    echo "- {$nodeId}: {$status}\n";
}

echo "\n";

// Example 7: Generating an Inventory Report
echo "Example 7: Generating an Inventory Report\n";

// Generate an inventory report
$report = $inventoryProcessingUnit->generateInventoryReport();

echo "Inventory Report:\n";
echo "Total Items: {$report['totalItems']}\n";
echo "Total Value: \${$report['totalValue']}\n";
echo "Low Stock Items: " . count($report['lowStockItems']) . "\n";

if (!empty($report['lowStockItems'])) {
    echo "Low Stock Items:\n";
    foreach ($report['lowStockItems'] as $item) {
        echo "- {$item['name']} (ID: {$item['productId']}, Quantity: {$item['quantity']})\n";
    }
}

echo "\n";

// Example 8: Restocking Inventory
echo "Example 8: Restocking Inventory\n";

// Restock the keyboard
$inventoryProcessingUnit->restockItem('prod-5', 10);
echo "Keyboard restocked\n";

// Read the updated inventory item
$keyboardFromSpace = $space->read(['productId' => 'prod-5']);
if ($keyboardFromSpace instanceof InventoryItem) {
    echo "Updated keyboard from space: {$keyboardFromSpace->getName()} (Quantity: {$keyboardFromSpace->getQuantity()})\n";
}

echo "\n";

// Clean up
$middleware->stopProcessingUnits();
$eventProcessor->stop();

echo "Middleware and Event Processor stopped\n";
echo "Example completed successfully\n";
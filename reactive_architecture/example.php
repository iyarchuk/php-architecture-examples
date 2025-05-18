<?php

namespace ReactiveArchitecture;

use ReactiveArchitecture\Core\EventStream;
use ReactiveArchitecture\Core\SimpleEventSubscriber;
use ReactiveArchitecture\Events\StockPriceEvent;
use ReactiveArchitecture\Publishers\StockPricePublisher;
use ReactiveArchitecture\Subscribers\PriceAlertSubscriber;
use ReactiveArchitecture\Processors\ReactiveProcessor;
use ReactiveArchitecture\Processors\TradingVolumeProcessor;

// Load base classes first
require_once __DIR__ . '/Core/EventSubscriber.php';

// Autoload classes
spl_autoload_register(function ($class) {
    // Only handle classes in our namespace
    if (strpos($class, 'ReactiveArchitecture\\') !== 0) {
        return;
    }

    // Convert namespace to file path
    $classWithoutNamespace = str_replace('ReactiveArchitecture\\', '', $class);

    // Check if this is a namespaced class within our architecture
    $parts = explode('\\', $classWithoutNamespace);
    $className = end($parts);

    // Map class names to their new locations
    $classMap = [
        'EventStream' => '/Core/EventStream.php',
        'EventPublisher' => '/Core/EventPublisher.php',
        'StockPriceEvent' => '/Events/StockPriceEvent.php',
        'StockPricePublisher' => '/Publishers/StockPricePublisher.php',
        'PriceAlertSubscriber' => '/Subscribers/PriceAlertSubscriber.php',
        'ReactiveProcessor' => '/Processors/ReactiveProcessor.php',
        'TradingVolumeProcessor' => '/Processors/TradingVolumeProcessor.php',
        'SimpleEventSubscriber' => '/Core/EventSubscriber.php'
    ];

    if (isset($classMap[$className])) {
        $file = __DIR__ . $classMap[$className];
    } else {
        // Default path for any unmapped classes
        $file = __DIR__ . '/' . str_replace('\\', '/', $classWithoutNamespace) . '.php';
    }

    if (file_exists($file)) {
        require_once $file;
    }
});

// Create a function to display messages
function displayMessage($message) {
    echo $message . PHP_EOL;
}

echo "=== Reactive Architecture Example: Stock Price Monitor ===" . PHP_EOL . PHP_EOL;

// 1. Create a stock price publisher
echo "Creating stock price publisher..." . PHP_EOL;
$publisher = new StockPricePublisher(
    3.0,    // 3% change is significant
    500000  // 500,000 shares is high volume
);

// 2. Create a price alert subscriber
echo "Creating price alert subscriber..." . PHP_EOL;
$alertSubscriber = new PriceAlertSubscriber(
    function ($message, $event) {
        displayMessage($message . " | " . $event);
    },
    10  // Limit to 10 alerts
);

// Set up price thresholds for specific stocks
$alertSubscriber->watchStock('AAPL', 150.0, 200.0);
$alertSubscriber->watchStock('GOOGL', 2500.0, 3000.0);
$alertSubscriber->watchStock('MSFT', 300.0, 350.0);

// 3. Register the subscriber with the publisher
echo "Registering subscriber with publisher..." . PHP_EOL;
$publisher->subscribe(StockPricePublisher::EVENT_PRICE_UPDATE, $alertSubscriber);
$publisher->subscribe(StockPricePublisher::EVENT_SIGNIFICANT_CHANGE, $alertSubscriber);
$publisher->subscribe(StockPricePublisher::EVENT_HIGH_VOLUME, $alertSubscriber);

// 4. Create a trading volume processor
echo "Creating trading volume processor..." . PHP_EOL;
$volumeProcessor = new TradingVolumeProcessor(100, 3); // batchSize: 100, windowSize: 3
$volumeProcessor->subscribeToPublisher($publisher);

// Subscribe to volume spikes
$volumeProcessor->getVolumeSpikes()->subscribe(function ($data) {
    displayMessage(sprintf(
        "VOLUME SPIKE: %s trading at %.2fx normal volume (%d shares)",
        $data['symbol'],
        $data['volumeRatio'],
        $data['volume']
    ));
});

echo PHP_EOL . "=== Simulating Stock Price Updates ===" . PHP_EOL . PHP_EOL;

// 5. Simulate some stock price updates
$stocks = [
    'AAPL' => ['price' => 175.0, 'volatility' => 2.0, 'volumeBase' => 200000],
    'GOOGL' => ['price' => 2700.0, 'volatility' => 5.0, 'volumeBase' => 100000],
    'MSFT' => ['price' => 320.0, 'volatility' => 1.5, 'volumeBase' => 150000],
    'AMZN' => ['price' => 3300.0, 'volatility' => 3.0, 'volumeBase' => 120000],
    'TSLA' => ['price' => 900.0, 'volatility' => 7.0, 'volumeBase' => 300000]
];

// Simulate 20 updates
for ($i = 1; $i <= 20; $i++) {
    echo "--- Update Cycle $i ---" . PHP_EOL;

    // Update each stock
    foreach ($stocks as $symbol => &$data) {
        // Calculate new price with random movement
        $movement = (mt_rand(-100, 100) / 100) * $data['volatility'];
        $newPrice = max(0.01, $data['price'] * (1 + $movement / 100));

        // Calculate volume with occasional spikes
        $volumeMultiplier = mt_rand(1, 20) === 1 ? mt_rand(3, 10) : 1;
        $volume = (int)($data['volumeBase'] * (0.5 + mt_rand(0, 100) / 100) * $volumeMultiplier);

        // Publish the update
        $event = $publisher->publishPriceUpdate($symbol, $newPrice, $volume);

        // Update the stored price
        $data['price'] = $newPrice;

        // Display the update
        echo "Updated $symbol: " . $event . PHP_EOL;
    }

    echo PHP_EOL;

    // Simulate some processing time
    usleep(100000);  // 100ms
}

echo "=== Final Stock Prices ===" . PHP_EOL;
foreach ($publisher->getAllLastPrices() as $symbol => $price) {
    echo "$symbol: $price" . PHP_EOL;
}

echo PHP_EOL . "=== Volume Moving Averages ===" . PHP_EOL;
foreach ($volumeProcessor->getAllMovingAverages() as $symbol => $average) {
    echo "$symbol: " . number_format($average) . " shares" . PHP_EOL;
}

echo PHP_EOL . "=== Summary ===" . PHP_EOL;
echo "Total alerts generated: " . $alertSubscriber->getAlertCount() . PHP_EOL;
echo "Subscriber active: " . ($alertSubscriber->isActive() ? 'Yes' : 'No') . PHP_EOL;

echo PHP_EOL . "=== End of Simulation ===" . PHP_EOL;

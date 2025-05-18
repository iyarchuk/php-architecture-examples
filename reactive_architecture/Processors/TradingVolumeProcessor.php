<?php

namespace ReactiveArchitecture\Processors;

use ReactiveArchitecture\Core\EventStream;
use ReactiveArchitecture\Core\SimpleEventSubscriber;
use ReactiveArchitecture\Events\StockPriceEvent;
use ReactiveArchitecture\Publishers\StockPricePublisher;

/**
 * TradingVolumeProcessor processes trading volume data reactively.
 * It demonstrates how to use ReactiveProcessor for specific domain tasks.
 */
class TradingVolumeProcessor
{
    /** @var \ReactiveArchitecture\Processors\ReactiveProcessor Underlying processor */
    private $processor;

    /** @var EventStream Output stream for processed volume data */
    private $outputStream;

    /** @var array Volume data by symbol */
    private $volumeData = [];

    /** @var array Moving averages by symbol */
    private $movingAverages = [];

    /** @var int Window size for moving average */
    private $windowSize;

    /**
     * Constructor
     * 
     * @param int $batchSize Maximum number of items to process at once
     * @param int $windowSize Window size for moving average calculation
     */
    public function __construct(int $batchSize = 100, int $windowSize = 5)
    {
        $this->windowSize = $windowSize;
        $this->outputStream = new EventStream();

        // Create processor with error handling
        $this->processor = \ReactiveArchitecture\Processors\ReactiveProcessor::withErrorHandling(
            function ($item, \Exception $e) {
                // Log error and return null
                error_log("Error processing volume data: " . $e->getMessage());
                return null;
            }
        );

        // Add processing stages
        $this->processor
            ->addStage([$this, 'extractVolumeData'])
            ->addStage([$this, 'calculateMovingAverage'])
            ->addStage([$this, 'detectVolumeSpikes']);
    }

    /**
     * Subscribe to a publisher's price update events
     * 
     * @param StockPricePublisher $publisher Publisher to subscribe to
     * @return self
     */
    public function subscribeToPublisher(StockPricePublisher $publisher): self
    {
        $subscriber = new SimpleEventSubscriber();

        // Subscribe to price updates
        $subscriber->on(StockPricePublisher::EVENT_PRICE_UPDATE, function (StockPriceEvent $event) {
            // Add event to input stream
            $this->processVolumeEvent($event);
        });

        // Register subscriber with publisher
        $publisher->subscribe(StockPricePublisher::EVENT_PRICE_UPDATE, $subscriber);

        return $this;
    }

    /**
     * Process a volume event
     * 
     * @param StockPriceEvent $event Event to process
     * @return void
     */
    public function processVolumeEvent(StockPriceEvent $event): void
    {
        $symbol = $event->getSymbol();
        $volume = $event->getVolume();

        // Store volume data
        if (!isset($this->volumeData[$symbol])) {
            $this->volumeData[$symbol] = [];
        }

        $this->volumeData[$symbol][] = [
            'timestamp' => $event->getTimestamp(),
            'volume' => $volume
        ];

        // Keep only the most recent data points
        if (count($this->volumeData[$symbol]) > $this->windowSize * 2) {
            array_shift($this->volumeData[$symbol]);
        }

        // Process the event through the processor
        $inputStream = new EventStream();
        $inputStream->push($event);
        $this->processor->process($inputStream);
    }

    /**
     * Extract volume data from event
     * 
     * @param StockPriceEvent $event Event to process
     * @return array|null Volume data or null if invalid
     */
    public function extractVolumeData(StockPriceEvent $event): ?array
    {
        if (!$event instanceof StockPriceEvent) {
            return null;
        }

        return [
            'symbol' => $event->getSymbol(),
            'volume' => $event->getVolume(),
            'timestamp' => $event->getTimestamp(),
            'price' => $event->getPrice()
        ];
    }

    /**
     * Calculate moving average of volume
     * 
     * @param array|null $data Volume data
     * @return array|null Data with moving average or null if invalid
     */
    public function calculateMovingAverage(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        $symbol = $data['symbol'];

        // Get volume history for this symbol
        $volumes = array_column($this->volumeData[$symbol] ?? [], 'volume');

        // Calculate moving average if we have enough data
        if (count($volumes) >= $this->windowSize) {
            $recentVolumes = array_slice($volumes, -$this->windowSize);
            $average = array_sum($recentVolumes) / count($recentVolumes);

            // Store moving average
            $this->movingAverages[$symbol] = $average;

            // Add to data
            $data['volumeMA'] = $average;
        } else {
            $data['volumeMA'] = $data['volume']; // Use current volume if not enough history
        }

        return $data;
    }

    /**
     * Detect volume spikes
     * 
     * @param array|null $data Volume data with moving average
     * @return array|null Data with spike detection or null if invalid
     */
    public function detectVolumeSpikes(?array $data): ?array
    {
        if ($data === null || !isset($data['volumeMA'])) {
            return null;
        }

        $currentVolume = $data['volume'];
        $averageVolume = $data['volumeMA'];

        // Calculate ratio of current volume to moving average
        $ratio = $averageVolume > 0 ? $currentVolume / $averageVolume : 1;

        // Detect spike (volume more than 2x the moving average)
        $data['isVolumeSpike'] = $ratio >= 2;
        $data['volumeRatio'] = $ratio;

        // If it's a spike, push to output stream
        if ($data['isVolumeSpike']) {
            $this->outputStream->push($data);
        }

        return $data;
    }

    /**
     * Get output stream of volume spikes
     * 
     * @return EventStream
     */
    public function getVolumeSpikes(): EventStream
    {
        return $this->outputStream;
    }

    /**
     * Get moving average for a symbol
     * 
     * @param string $symbol Stock symbol
     * @return float|null Moving average or null if not available
     */
    public function getMovingAverage(string $symbol): ?float
    {
        return $this->movingAverages[$symbol] ?? null;
    }

    /**
     * Get all moving averages
     * 
     * @return array Moving averages by symbol
     */
    public function getAllMovingAverages(): array
    {
        return $this->movingAverages;
    }
}

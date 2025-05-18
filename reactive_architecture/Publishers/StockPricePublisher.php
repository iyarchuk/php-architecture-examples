<?php

namespace ReactiveArchitecture\Publishers;

use ReactiveArchitecture\Core\EventPublisher;
use ReactiveArchitecture\Events\StockPriceEvent;

/**
 * StockPricePublisher is responsible for publishing stock price updates.
 * It extends the EventPublisher class to provide stock-specific functionality.
 */
class StockPricePublisher extends EventPublisher
{
    /** @var string Event type for price updates */
    const EVENT_PRICE_UPDATE = 'price_update';
    
    /** @var string Event type for significant price changes */
    const EVENT_SIGNIFICANT_CHANGE = 'significant_change';
    
    /** @var string Event type for high volume trading */
    const EVENT_HIGH_VOLUME = 'high_volume';
    
    /** @var float Threshold for significant price changes (percentage) */
    private $significantChangeThreshold;
    
    /** @var int Threshold for high volume trading */
    private $highVolumeThreshold;
    
    /** @var array Last prices by symbol */
    private $lastPrices = [];
    
    /**
     * Constructor
     * 
     * @param float $significantChangeThreshold Threshold for significant price changes
     * @param int $highVolumeThreshold Threshold for high volume trading
     * @param bool $bufferEvents Whether to buffer events
     * @param int $maxBufferSize Maximum buffer size
     */
    public function __construct(
        float $significantChangeThreshold = 5.0,
        int $highVolumeThreshold = 1000000,
        bool $bufferEvents = false,
        int $maxBufferSize = 1000
    ) {
        parent::__construct($bufferEvents, $maxBufferSize);
        $this->significantChangeThreshold = $significantChangeThreshold;
        $this->highVolumeThreshold = $highVolumeThreshold;
    }
    
    /**
     * Publish a stock price update
     * 
     * @param string $symbol Stock symbol
     * @param float $price Current price
     * @param int $volume Trading volume
     * @return StockPriceEvent The created event
     */
    public function publishPriceUpdate(string $symbol, float $price, int $volume): StockPriceEvent
    {
        // Get previous price or default to current price
        $previousPrice = $this->lastPrices[$symbol] ?? $price;
        
        // Create event
        $event = new StockPriceEvent($symbol, $price, $previousPrice, $volume);
        
        // Update last price
        $this->lastPrices[$symbol] = $price;
        
        // Publish price update event
        $this->publish(self::EVENT_PRICE_UPDATE, $event);
        
        // Check for significant price change
        if ($event->isSignificantChange($this->significantChangeThreshold)) {
            $this->publish(self::EVENT_SIGNIFICANT_CHANGE, $event);
        }
        
        // Check for high volume trading
        if ($volume >= $this->highVolumeThreshold) {
            $this->publish(self::EVENT_HIGH_VOLUME, $event);
        }
        
        return $event;
    }
    
    /**
     * Set threshold for significant price changes
     * 
     * @param float $threshold Threshold percentage
     * @return self
     */
    public function setSignificantChangeThreshold(float $threshold): self
    {
        $this->significantChangeThreshold = $threshold;
        return $this;
    }
    
    /**
     * Set threshold for high volume trading
     * 
     * @param int $threshold Threshold volume
     * @return self
     */
    public function setHighVolumeThreshold(int $threshold): self
    {
        $this->highVolumeThreshold = $threshold;
        return $this;
    }
    
    /**
     * Get last price for a stock
     * 
     * @param string $symbol Stock symbol
     * @return float|null Last price or null if not available
     */
    public function getLastPrice(string $symbol): ?float
    {
        return $this->lastPrices[$symbol] ?? null;
    }
    
    /**
     * Get all last prices
     * 
     * @return array Last prices by symbol
     */
    public function getAllLastPrices(): array
    {
        return $this->lastPrices;
    }
}
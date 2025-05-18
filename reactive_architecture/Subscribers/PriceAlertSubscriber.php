<?php

namespace ReactiveArchitecture\Subscribers;

use ReactiveArchitecture\Core\AbstractEventSubscriber;
use ReactiveArchitecture\Events\StockPriceEvent;
use ReactiveArchitecture\Publishers\StockPricePublisher;

/**
 * PriceAlertSubscriber reacts to price changes and generates alerts.
 * It demonstrates how to implement a subscriber that processes specific events.
 */
class PriceAlertSubscriber extends AbstractEventSubscriber
{
    /** @var array Watched stocks with alert thresholds */
    private $watchedStocks = [];
    
    /** @var callable Alert callback function */
    private $alertCallback;
    
    /** @var int Number of alerts generated */
    private $alertCount = 0;
    
    /** @var int Maximum number of alerts to generate */
    private $maxAlerts;
    
    /**
     * Constructor
     * 
     * @param callable $alertCallback Function to call when an alert is triggered
     * @param int $maxAlerts Maximum number of alerts to generate (0 for unlimited)
     */
    public function __construct(callable $alertCallback, int $maxAlerts = 0)
    {
        $this->alertCallback = $alertCallback;
        $this->maxAlerts = $maxAlerts;
        
        // Register handlers for stock price events
        $this->on(StockPricePublisher::EVENT_PRICE_UPDATE, [$this, 'handlePriceUpdate']);
        $this->on(StockPricePublisher::EVENT_SIGNIFICANT_CHANGE, [$this, 'handleSignificantChange']);
        $this->on(StockPricePublisher::EVENT_HIGH_VOLUME, [$this, 'handleHighVolume']);
    }
    
    /**
     * Add a stock to watch
     * 
     * @param string $symbol Stock symbol
     * @param float $minPrice Minimum price to trigger alert
     * @param float $maxPrice Maximum price to trigger alert
     * @return self
     */
    public function watchStock(string $symbol, float $minPrice = 0, float $maxPrice = PHP_FLOAT_MAX): self
    {
        $this->watchedStocks[$symbol] = [
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice
        ];
        
        return $this;
    }
    
    /**
     * Stop watching a stock
     * 
     * @param string $symbol Stock symbol
     * @return self
     */
    public function unwatchStock(string $symbol): self
    {
        if (isset($this->watchedStocks[$symbol])) {
            unset($this->watchedStocks[$symbol]);
        }
        
        return $this;
    }
    
    /**
     * Handle price update event
     * 
     * @param StockPriceEvent $event Price update event
     * @return void
     */
    public function handlePriceUpdate(StockPriceEvent $event): void
    {
        $symbol = $event->getSymbol();
        $price = $event->getPrice();
        
        // Check if this stock is being watched
        if (!isset($this->watchedStocks[$symbol])) {
            return;
        }
        
        $thresholds = $this->watchedStocks[$symbol];
        
        // Check if price is outside thresholds
        if ($price <= $thresholds['minPrice']) {
            $this->triggerAlert(
                "PRICE ALERT: {$symbol} has fallen to {$price}, below minimum threshold of {$thresholds['minPrice']}",
                $event
            );
        } elseif ($price >= $thresholds['maxPrice']) {
            $this->triggerAlert(
                "PRICE ALERT: {$symbol} has risen to {$price}, above maximum threshold of {$thresholds['maxPrice']}",
                $event
            );
        }
    }
    
    /**
     * Handle significant change event
     * 
     * @param StockPriceEvent $event Significant change event
     * @return void
     */
    public function handleSignificantChange(StockPriceEvent $event): void
    {
        $symbol = $event->getSymbol();
        $percentChange = $event->getPercentChange();
        $direction = $percentChange > 0 ? 'up' : 'down';
        
        $this->triggerAlert(
            "SIGNIFICANT CHANGE: {$symbol} has moved {$direction} by {$percentChange}%",
            $event
        );
    }
    
    /**
     * Handle high volume event
     * 
     * @param StockPriceEvent $event High volume event
     * @return void
     */
    public function handleHighVolume(StockPriceEvent $event): void
    {
        $symbol = $event->getSymbol();
        $volume = $event->getVolume();
        
        $this->triggerAlert(
            "HIGH VOLUME ALERT: {$symbol} is trading at high volume ({$volume} shares)",
            $event
        );
    }
    
    /**
     * Trigger an alert
     * 
     * @param string $message Alert message
     * @param StockPriceEvent $event Event that triggered the alert
     * @return void
     */
    private function triggerAlert(string $message, StockPriceEvent $event): void
    {
        // Check if maximum alerts has been reached
        if ($this->maxAlerts > 0 && $this->alertCount >= $this->maxAlerts) {
            // Pause this subscriber to prevent further alerts
            $this->pause();
            return;
        }
        
        // Call the alert callback
        call_user_func($this->alertCallback, $message, $event);
        
        // Increment alert count
        $this->alertCount++;
    }
    
    /**
     * Get number of alerts generated
     * 
     * @return int
     */
    public function getAlertCount(): int
    {
        return $this->alertCount;
    }
    
    /**
     * Reset alert count
     * 
     * @return self
     */
    public function resetAlertCount(): self
    {
        $this->alertCount = 0;
        return $this;
    }
}
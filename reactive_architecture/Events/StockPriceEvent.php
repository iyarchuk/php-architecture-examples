<?php

namespace ReactiveArchitecture\Events;

/**
 * StockPriceEvent represents a stock price change event.
 * It encapsulates all data related to a stock price update.
 */
class StockPriceEvent
{
    /** @var string Stock symbol */
    private $symbol;
    
    /** @var float Current price */
    private $price;
    
    /** @var float Previous price */
    private $previousPrice;
    
    /** @var float Percentage change */
    private $percentChange;
    
    /** @var int Trading volume */
    private $volume;
    
    /** @var \DateTime Timestamp */
    private $timestamp;
    
    /**
     * Constructor
     * 
     * @param string $symbol Stock symbol
     * @param float $price Current price
     * @param float $previousPrice Previous price
     * @param int $volume Trading volume
     */
    public function __construct(string $symbol, float $price, float $previousPrice, int $volume)
    {
        $this->symbol = $symbol;
        $this->price = $price;
        $this->previousPrice = $previousPrice;
        $this->volume = $volume;
        $this->timestamp = new \DateTime();
        
        // Calculate percentage change
        if ($previousPrice > 0) {
            $this->percentChange = (($price - $previousPrice) / $previousPrice) * 100;
        } else {
            $this->percentChange = 0;
        }
    }
    
    /**
     * Get stock symbol
     * 
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }
    
    /**
     * Get current price
     * 
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }
    
    /**
     * Get previous price
     * 
     * @return float
     */
    public function getPreviousPrice(): float
    {
        return $this->previousPrice;
    }
    
    /**
     * Get percentage change
     * 
     * @return float
     */
    public function getPercentChange(): float
    {
        return $this->percentChange;
    }
    
    /**
     * Get trading volume
     * 
     * @return int
     */
    public function getVolume(): int
    {
        return $this->volume;
    }
    
    /**
     * Get timestamp
     * 
     * @return \DateTime
     */
    public function getTimestamp(): \DateTime
    {
        return $this->timestamp;
    }
    
    /**
     * Check if price increased
     * 
     * @return bool
     */
    public function isPriceIncrease(): bool
    {
        return $this->price > $this->previousPrice;
    }
    
    /**
     * Check if price decreased
     * 
     * @return bool
     */
    public function isPriceDecrease(): bool
    {
        return $this->price < $this->previousPrice;
    }
    
    /**
     * Check if price change exceeds threshold
     * 
     * @param float $threshold Threshold percentage
     * @return bool
     */
    public function isSignificantChange(float $threshold): bool
    {
        return abs($this->percentChange) >= $threshold;
    }
    
    /**
     * Get string representation of the event
     * 
     * @return string
     */
    public function __toString(): string
    {
        $direction = $this->isPriceIncrease() ? '↑' : ($this->isPriceDecrease() ? '↓' : '→');
        return sprintf(
            "%s: %s %.2f (%.2f%%) %s | Vol: %d | %s",
            $this->symbol,
            $direction,
            $this->price,
            $this->percentChange,
            $this->previousPrice > 0 ? sprintf("from %.2f", $this->previousPrice) : '',
            $this->volume,
            $this->timestamp->format('Y-m-d H:i:s')
        );
    }
}
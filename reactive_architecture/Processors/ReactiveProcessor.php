<?php

namespace ReactiveArchitecture\Processors;

use ReactiveArchitecture\Core\EventStream;

/**
 * ReactiveProcessor provides functionality for processing data streams reactively.
 * It demonstrates how to handle backpressure and apply transformations to data streams.
 */
class ReactiveProcessor
{
    /** @var int Maximum number of items to process at once */
    private $batchSize;
    
    /** @var int Processing delay in microseconds */
    private $processingDelay;
    
    /** @var callable[] Processing stages */
    private $stages = [];
    
    /**
     * Constructor
     * 
     * @param int $batchSize Maximum number of items to process at once
     * @param int $processingDelay Processing delay in microseconds
     */
    public function __construct(int $batchSize = 100, int $processingDelay = 0)
    {
        $this->batchSize = $batchSize;
        $this->processingDelay = $processingDelay;
    }
    
    /**
     * Add a processing stage
     * 
     * @param callable $stage Processing function
     * @return self
     */
    public function addStage(callable $stage): self
    {
        $this->stages[] = $stage;
        return $this;
    }
    
    /**
     * Process a stream of data
     * 
     * @param EventStream $stream Input stream
     * @return EventStream Processed stream
     */
    public function process(EventStream $stream): EventStream
    {
        $result = new EventStream();
        $buffer = [];
        $count = 0;
        
        // Subscribe to the input stream
        $stream->subscribe(function ($item) use (&$buffer, &$count, &$result) {
            $buffer[] = $item;
            $count++;
            
            // Process in batches to handle backpressure
            if ($count >= $this->batchSize) {
                $this->processBatch($buffer, $result);
                $buffer = [];
                $count = 0;
            }
        });
        
        // Process any remaining items
        if (!empty($buffer)) {
            $this->processBatch($buffer, $result);
        }
        
        return $result;
    }
    
    /**
     * Process a batch of items through all stages
     * 
     * @param array $batch Items to process
     * @param EventStream $output Output stream
     * @return void
     */
    private function processBatch(array $batch, EventStream $output): void
    {
        // Apply each stage to the batch
        foreach ($this->stages as $stage) {
            $batch = array_map($stage, $batch);
            
            // Simulate processing time for demonstration
            if ($this->processingDelay > 0) {
                usleep($this->processingDelay);
            }
        }
        
        // Push processed items to the output stream
        foreach ($batch as $item) {
            $output->push($item);
        }
    }
    
    /**
     * Create a processor with error handling
     * 
     * @param callable $errorHandler Function to handle errors
     * @return self
     */
    public static function withErrorHandling(callable $errorHandler): self
    {
        $processor = new self();
        
        // Wrap each stage with error handling
        $processor->addStage(function ($item) use ($errorHandler) {
            try {
                // Process the item
                return $item;
            } catch (\Exception $e) {
                // Handle the error
                return $errorHandler($item, $e);
            }
        });
        
        return $processor;
    }
    
    /**
     * Create a processor with retry capability
     * 
     * @param int $maxRetries Maximum number of retries
     * @param int $delay Delay between retries in microseconds
     * @return self
     */
    public static function withRetry(int $maxRetries = 3, int $delay = 1000000): self
    {
        $processor = new self();
        
        // Add retry logic
        $processor->addStage(function ($item) use ($maxRetries, $delay) {
            $retries = 0;
            
            while ($retries <= $maxRetries) {
                try {
                    // Process the item
                    return $item;
                } catch (\Exception $e) {
                    $retries++;
                    
                    if ($retries > $maxRetries) {
                        throw $e;
                    }
                    
                    // Wait before retrying
                    usleep($delay);
                }
            }
            
            return $item;
        });
        
        return $processor;
    }
}
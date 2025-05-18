<?php

namespace PipeAndFilterArchitecture;

use PipeAndFilterArchitecture\Filter\FilterInterface;
use PipeAndFilterArchitecture\Pipe\PipeInterface;
use PipeAndFilterArchitecture\Pipe\SimplePipe;

/**
 * Pipeline connects multiple filters together to form a processing pipeline.
 */
class Pipeline
{
    /**
     * @var array Array of filters in the pipeline
     */
    private $filters = [];
    
    /**
     * Add a filter to the pipeline.
     *
     * @param FilterInterface $filter The filter to add
     * @return Pipeline Returns $this for method chaining
     */
    public function addFilter(FilterInterface $filter): Pipeline
    {
        $this->filters[] = $filter;
        return $this;
    }
    
    /**
     * Process data through the pipeline.
     *
     * @param mixed $input The input data to process
     * @return mixed The result after processing through all filters
     */
    public function process($input)
    {
        if (empty($this->filters)) {
            return $input;
        }
        
        $output = $input;
        
        foreach ($this->filters as $filter) {
            $output = $filter->process($output);
        }
        
        return $output;
    }
    
    /**
     * Create a pipeline from an array of filters.
     *
     * @param array $filters Array of FilterInterface objects
     * @return Pipeline A new pipeline with the specified filters
     */
    public static function create(array $filters = []): Pipeline
    {
        $pipeline = new self();
        
        foreach ($filters as $filter) {
            if (!$filter instanceof FilterInterface) {
                throw new \InvalidArgumentException("All elements must implement FilterInterface");
            }
            
            $pipeline->addFilter($filter);
        }
        
        return $pipeline;
    }
}
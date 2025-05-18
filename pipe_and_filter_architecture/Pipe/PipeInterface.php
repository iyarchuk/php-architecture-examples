<?php

namespace PipeAndFilterArchitecture\Pipe;

use PipeAndFilterArchitecture\Filter\FilterInterface;

/**
 * PipeInterface defines the contract for all pipes in the pipeline.
 * A pipe connects two filters and transfers data from one to another.
 */
interface PipeInterface
{
    /**
     * Register a filter to receive data from this pipe.
     *
     * @param FilterInterface $filter The filter to register
     * @return PipeInterface Returns $this for method chaining
     */
    public function registerFilter(FilterInterface $filter): PipeInterface;
    
    /**
     * Pass data through the pipe to the registered filter.
     *
     * @param mixed $data The data to pass through the pipe
     * @return mixed The result after the data has been processed by the filter
     */
    public function pass($data);
}
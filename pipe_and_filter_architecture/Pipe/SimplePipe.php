<?php

namespace PipeAndFilterArchitecture\Pipe;

use PipeAndFilterArchitecture\Filter\FilterInterface;

/**
 * SimplePipe is a basic implementation of the PipeInterface.
 * It connects a single filter and passes data to it.
 */
class SimplePipe implements PipeInterface
{
    /**
     * @var FilterInterface The filter that will process data passed through this pipe
     */
    private $filter;

    /**
     * Register a filter to receive data from this pipe.
     *
     * @param FilterInterface $filter The filter to register
     * @return PipeInterface Returns $this for method chaining
     */
    public function registerFilter(FilterInterface $filter): PipeInterface
    {
        $this->filter = $filter;
        return $this;
    }
    
    /**
     * Pass data through the pipe to the registered filter.
     *
     * @param mixed $data The data to pass through the pipe
     * @return mixed The result after the data has been processed by the filter
     * @throws \RuntimeException If no filter is registered
     */
    public function pass($data)
    {
        if (!$this->filter) {
            throw new \RuntimeException("No filter registered with this pipe");
        }
        
        return $this->filter->process($data);
    }
}
<?php

namespace PipeAndFilterArchitecture\Filter;

/**
 * FilterInterface defines the contract for all filters in the pipeline.
 * A filter processes input data and produces output data.
 */
interface FilterInterface
{
    /**
     * Process the input data and return the processed output.
     *
     * @param mixed $input The input data to process
     * @return mixed The processed output data
     */
    public function process($input);
}
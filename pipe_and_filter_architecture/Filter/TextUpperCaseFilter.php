<?php

namespace PipeAndFilterArchitecture\Filter;

/**
 * TextUpperCaseFilter converts text to uppercase.
 */
class TextUpperCaseFilter implements FilterInterface
{
    /**
     * Process the input text by converting it to uppercase.
     *
     * @param string $input The input text to process
     * @return string The uppercase text
     */
    public function process($input)
    {
        if (!is_string($input)) {
            throw new \InvalidArgumentException("Input must be a string");
        }
        
        return strtoupper($input);
    }
}
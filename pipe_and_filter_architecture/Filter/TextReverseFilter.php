<?php

namespace PipeAndFilterArchitecture\Filter;

/**
 * TextReverseFilter reverses the input text.
 */
class TextReverseFilter implements FilterInterface
{
    /**
     * Process the input text by reversing it.
     *
     * @param string $input The input text to process
     * @return string The reversed text
     */
    public function process($input)
    {
        if (!is_string($input)) {
            throw new \InvalidArgumentException("Input must be a string");
        }
        
        return strrev($input);
    }
}
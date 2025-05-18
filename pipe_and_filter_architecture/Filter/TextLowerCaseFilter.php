<?php

namespace PipeAndFilterArchitecture\Filter;

/**
 * TextLowerCaseFilter converts text to lowercase.
 */
class TextLowerCaseFilter implements FilterInterface
{
    /**
     * Process the input text by converting it to lowercase.
     *
     * @param string $input The input text to process
     * @return string The lowercase text
     */
    public function process($input)
    {
        if (!is_string($input)) {
            throw new \InvalidArgumentException("Input must be a string");
        }
        
        return strtolower($input);
    }
}
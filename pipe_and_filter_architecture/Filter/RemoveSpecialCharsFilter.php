<?php

namespace PipeAndFilterArchitecture\Filter;

/**
 * RemoveSpecialCharsFilter removes special characters from the input text.
 */
class RemoveSpecialCharsFilter implements FilterInterface
{
    /**
     * Process the input text by removing special characters.
     *
     * @param string $input The input text to process
     * @return string The text with special characters removed
     */
    public function process($input)
    {
        if (!is_string($input)) {
            throw new \InvalidArgumentException("Input must be a string");
        }
        
        // Remove all characters except letters, numbers, and spaces
        return preg_replace('/[^a-zA-Z0-9\s]/', '', $input);
    }
}
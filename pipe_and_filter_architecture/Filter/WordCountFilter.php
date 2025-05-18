<?php

namespace PipeAndFilterArchitecture\Filter;

/**
 * WordCountFilter counts the number of words in the input text.
 */
class WordCountFilter implements FilterInterface
{
    /**
     * Process the input text by counting the number of words.
     *
     * @param string $input The input text to process
     * @return array An array containing the original text and the word count
     */
    public function process($input)
    {
        if (!is_string($input)) {
            throw new \InvalidArgumentException("Input must be a string");
        }
        
        $wordCount = str_word_count($input);
        
        return [
            'text' => $input,
            'word_count' => $wordCount
        ];
    }
}
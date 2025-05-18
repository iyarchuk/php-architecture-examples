<?php

namespace BlackboardArchitecture\KnowledgeSources;

use BlackboardArchitecture\Blackboard\Blackboard;

/**
 * NameAnalyzer
 * 
 * This class represents a knowledge source that analyzes and validates user names.
 * It checks if the name is valid, extracts first and last names, and provides feedback.
 */
class NameAnalyzer extends KnowledgeSource
{
    /**
     * @var int The minimum length of a valid name
     */
    private int $minLength;
    
    /**
     * @var int The maximum length of a valid name
     */
    private int $maxLength;
    
    /**
     * Constructor
     * 
     * @param int $minLength The minimum length of a valid name
     * @param int $maxLength The maximum length of a valid name
     */
    public function __construct(int $minLength = 2, int $maxLength = 50)
    {
        parent::__construct('NameAnalyzer');
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
    }
    
    /**
     * Check if the knowledge source can contribute to the current state of the blackboard
     * 
     * @param Blackboard $blackboard The blackboard
     * @return bool True if the knowledge source can contribute, false otherwise
     */
    public function canContribute(Blackboard $blackboard): bool
    {
        // Can contribute if there's a name to analyze and it hasn't been analyzed yet
        return $blackboard->has('name') && !$blackboard->has('name_analyzed');
    }
    
    /**
     * Contribute to the blackboard
     * 
     * @param Blackboard $blackboard The blackboard
     * @return void
     */
    public function contribute(Blackboard $blackboard): void
    {
        $name = $blackboard->get('name');
        
        $this->log($blackboard, "Analyzing name: $name");
        
        // Validate name length
        $nameLength = strlen($name);
        if ($nameLength < $this->minLength) {
            $blackboard->set('name_valid', false, $this->name);
            $blackboard->set('name_error', "Name is too short (minimum {$this->minLength} characters)", $this->name);
            $blackboard->set('name_analyzed', true, $this->name);
            $this->log($blackboard, "Name is too short");
            return;
        }
        
        if ($nameLength > $this->maxLength) {
            $blackboard->set('name_valid', false, $this->name);
            $blackboard->set('name_error', "Name is too long (maximum {$this->maxLength} characters)", $this->name);
            $blackboard->set('name_analyzed', true, $this->name);
            $this->log($blackboard, "Name is too long");
            return;
        }
        
        // Check for invalid characters
        if (!preg_match('/^[a-zA-Z\s\'-]+$/', $name)) {
            $blackboard->set('name_valid', false, $this->name);
            $blackboard->set('name_error', "Name contains invalid characters", $this->name);
            $blackboard->set('name_analyzed', true, $this->name);
            $this->log($blackboard, "Name contains invalid characters");
            return;
        }
        
        // Extract first and last name
        $nameParts = explode(' ', $name, 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';
        
        $blackboard->set('first_name', $firstName, $this->name);
        
        if (!empty($lastName)) {
            $blackboard->set('last_name', $lastName, $this->name);
        }
        
        // Set name as valid
        $blackboard->set('name_valid', true, $this->name);
        $blackboard->set('name_analyzed', true, $this->name);
        
        // Provide feedback
        if (empty($lastName)) {
            $blackboard->set('name_feedback', "Only first name provided. Consider adding a last name.", $this->name);
            $this->log($blackboard, "Only first name provided");
        } else {
            $this->log($blackboard, "Name is valid and complete");
        }
    }
    
    /**
     * Get the priority of the knowledge source
     * 
     * @return int
     */
    public function getPriority(): int
    {
        return 10; // High priority - name validation should happen early
    }
}
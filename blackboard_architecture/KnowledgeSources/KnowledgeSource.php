<?php

namespace BlackboardArchitecture\KnowledgeSources;

use BlackboardArchitecture\Blackboard\Blackboard;

/**
 * KnowledgeSource
 * 
 * This abstract class represents the base class for all knowledge sources in the Blackboard architecture.
 * Knowledge sources are specialized modules that contribute to solving the problem by updating the blackboard.
 */
abstract class KnowledgeSource
{
    /**
     * @var string The name of the knowledge source
     */
    protected string $name;
    
    /**
     * Constructor
     * 
     * @param string $name The name of the knowledge source
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    /**
     * Get the name of the knowledge source
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Check if the knowledge source can contribute to the current state of the blackboard
     * 
     * @param Blackboard $blackboard The blackboard
     * @return bool True if the knowledge source can contribute, false otherwise
     */
    abstract public function canContribute(Blackboard $blackboard): bool;
    
    /**
     * Contribute to the blackboard
     * 
     * @param Blackboard $blackboard The blackboard
     * @return void
     */
    abstract public function contribute(Blackboard $blackboard): void;
    
    /**
     * Get the priority of the knowledge source
     * Higher priority knowledge sources are activated first when multiple sources can contribute
     * 
     * @return int
     */
    public function getPriority(): int
    {
        return 0; // Default priority
    }
    
    /**
     * Log a message to the blackboard
     * 
     * @param Blackboard $blackboard The blackboard
     * @param string $message The message to log
     * @return void
     */
    protected function log(Blackboard $blackboard, string $message): void
    {
        $blackboard->addNote($message, $this->name);
    }
}
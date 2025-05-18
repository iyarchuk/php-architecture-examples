<?php

namespace BlackboardArchitecture\Control;

use BlackboardArchitecture\Blackboard\Blackboard;
use BlackboardArchitecture\KnowledgeSources\KnowledgeSource;

/**
 * Controller
 * 
 * This class represents the Control component in the Blackboard architecture.
 * It orchestrates the problem-solving process by selecting which knowledge source to activate next.
 */
class Controller
{
    /**
     * @var Blackboard The blackboard
     */
    private Blackboard $blackboard;
    
    /**
     * @var array The knowledge sources
     */
    private array $knowledgeSources = [];
    
    /**
     * @var int The maximum number of iterations
     */
    private int $maxIterations;
    
    /**
     * @var bool Whether to stop when no knowledge source can contribute
     */
    private bool $stopWhenNoContribution;
    
    /**
     * @var array The execution log
     */
    private array $executionLog = [];
    
    /**
     * Constructor
     * 
     * @param Blackboard $blackboard The blackboard
     * @param int $maxIterations The maximum number of iterations
     * @param bool $stopWhenNoContribution Whether to stop when no knowledge source can contribute
     */
    public function __construct(Blackboard $blackboard, int $maxIterations = 100, bool $stopWhenNoContribution = true)
    {
        $this->blackboard = $blackboard;
        $this->maxIterations = $maxIterations;
        $this->stopWhenNoContribution = $stopWhenNoContribution;
    }
    
    /**
     * Add a knowledge source
     * 
     * @param KnowledgeSource $knowledgeSource The knowledge source to add
     * @return void
     */
    public function addKnowledgeSource(KnowledgeSource $knowledgeSource): void
    {
        $this->knowledgeSources[] = $knowledgeSource;
    }
    
    /**
     * Add multiple knowledge sources
     * 
     * @param array $knowledgeSources The knowledge sources to add
     * @return void
     */
    public function addKnowledgeSources(array $knowledgeSources): void
    {
        foreach ($knowledgeSources as $knowledgeSource) {
            if ($knowledgeSource instanceof KnowledgeSource) {
                $this->addKnowledgeSource($knowledgeSource);
            }
        }
    }
    
    /**
     * Run the controller
     * 
     * @return bool True if the problem was solved, false otherwise
     */
    public function run(): bool
    {
        $iteration = 0;
        $this->executionLog = [];
        
        $this->log("Starting problem-solving process");
        
        while ($iteration < $this->maxIterations) {
            $iteration++;
            $this->log("Iteration $iteration");
            
            // Find knowledge sources that can contribute
            $contributingSources = $this->findContributingSources();
            
            if (empty($contributingSources)) {
                $this->log("No knowledge sources can contribute");
                
                if ($this->stopWhenNoContribution) {
                    $this->log("Stopping because no knowledge source can contribute");
                    break;
                }
            } else {
                // Select the best knowledge source
                $selectedSource = $this->selectBestKnowledgeSource($contributingSources);
                
                $this->log("Selected knowledge source: " . $selectedSource->getName());
                
                // Apply the selected knowledge source
                $selectedSource->contribute($this->blackboard);
                
                // Check if the problem is solved
                if ($this->isProblemSolved()) {
                    $this->log("Problem solved after $iteration iterations");
                    return true;
                }
            }
        }
        
        if ($iteration >= $this->maxIterations) {
            $this->log("Reached maximum number of iterations ($this->maxIterations)");
        }
        
        return $this->isProblemSolved();
    }
    
    /**
     * Find knowledge sources that can contribute
     * 
     * @return array The knowledge sources that can contribute
     */
    private function findContributingSources(): array
    {
        $contributingSources = [];
        
        foreach ($this->knowledgeSources as $knowledgeSource) {
            if ($knowledgeSource->canContribute($this->blackboard)) {
                $contributingSources[] = $knowledgeSource;
            }
        }
        
        return $contributingSources;
    }
    
    /**
     * Select the best knowledge source from the contributing sources
     * 
     * @param array $contributingSources The knowledge sources that can contribute
     * @return KnowledgeSource The best knowledge source
     */
    private function selectBestKnowledgeSource(array $contributingSources): KnowledgeSource
    {
        // Sort by priority (higher priority first)
        usort($contributingSources, function (KnowledgeSource $a, KnowledgeSource $b) {
            return $b->getPriority() - $a->getPriority();
        });
        
        // Return the highest priority knowledge source
        return $contributingSources[0];
    }
    
    /**
     * Check if the problem is solved
     * 
     * @return bool True if the problem is solved, false otherwise
     */
    private function isProblemSolved(): bool
    {
        // In this example, the problem is solved when the user profile is completed
        return $this->blackboard->has('profile_completed') && $this->blackboard->get('profile_completed') === true;
    }
    
    /**
     * Get the execution log
     * 
     * @return array The execution log
     */
    public function getExecutionLog(): array
    {
        return $this->executionLog;
    }
    
    /**
     * Log a message
     * 
     * @param string $message The message to log
     * @return void
     */
    private function log(string $message): void
    {
        $this->executionLog[] = [
            'timestamp' => date('Y-m-d H:i:s'),
            'message' => $message
        ];
    }
    
    /**
     * Get the blackboard
     * 
     * @return Blackboard The blackboard
     */
    public function getBlackboard(): Blackboard
    {
        return $this->blackboard;
    }
}
<?php

namespace BlackboardArchitecture\Blackboard;

/**
 * Blackboard
 * 
 * This class represents the central data structure in the Blackboard architecture.
 * It stores the current state of the problem-solving process and provides methods
 * for reading and updating the state.
 */
class Blackboard
{
    /**
     * @var array The current state of the blackboard
     */
    private array $state = [];
    
    /**
     * @var array The history of changes to the blackboard
     */
    private array $history = [];
    
    /**
     * @var int The current step in the problem-solving process
     */
    private int $currentStep = 0;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->history[] = [
            'step' => $this->currentStep,
            'action' => 'initialize',
            'state' => $this->state,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Set a value in the blackboard
     * 
     * @param string $key The key to set
     * @param mixed $value The value to set
     * @param string $source The knowledge source that set the value
     * @return void
     */
    public function set(string $key, $value, string $source): void
    {
        $oldValue = $this->state[$key] ?? null;
        $this->state[$key] = $value;
        
        $this->currentStep++;
        $this->history[] = [
            'step' => $this->currentStep,
            'action' => 'set',
            'key' => $key,
            'old_value' => $oldValue,
            'new_value' => $value,
            'source' => $source,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Get a value from the blackboard
     * 
     * @param string $key The key to get
     * @param mixed $default The default value if the key doesn't exist
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->state[$key] ?? $default;
    }
    
    /**
     * Check if a key exists in the blackboard
     * 
     * @param string $key The key to check
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->state[$key]);
    }
    
    /**
     * Remove a key from the blackboard
     * 
     * @param string $key The key to remove
     * @param string $source The knowledge source that removed the key
     * @return void
     */
    public function remove(string $key, string $source): void
    {
        if (isset($this->state[$key])) {
            $oldValue = $this->state[$key];
            unset($this->state[$key]);
            
            $this->currentStep++;
            $this->history[] = [
                'step' => $this->currentStep,
                'action' => 'remove',
                'key' => $key,
                'old_value' => $oldValue,
                'source' => $source,
                'timestamp' => date('Y-m-d H:i:s')
            ];
        }
    }
    
    /**
     * Get the entire state of the blackboard
     * 
     * @return array
     */
    public function getState(): array
    {
        return $this->state;
    }
    
    /**
     * Set multiple values in the blackboard
     * 
     * @param array $values The values to set
     * @param string $source The knowledge source that set the values
     * @return void
     */
    public function setMultiple(array $values, string $source): void
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $source);
        }
    }
    
    /**
     * Get the history of changes to the blackboard
     * 
     * @return array
     */
    public function getHistory(): array
    {
        return $this->history;
    }
    
    /**
     * Get the current step in the problem-solving process
     * 
     * @return int
     */
    public function getCurrentStep(): int
    {
        return $this->currentStep;
    }
    
    /**
     * Clear the blackboard
     * 
     * @param string $source The knowledge source that cleared the blackboard
     * @return void
     */
    public function clear(string $source): void
    {
        $oldState = $this->state;
        $this->state = [];
        
        $this->currentStep++;
        $this->history[] = [
            'step' => $this->currentStep,
            'action' => 'clear',
            'old_state' => $oldState,
            'source' => $source,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Add a note to the history without changing the state
     * 
     * @param string $note The note to add
     * @param string $source The knowledge source that added the note
     * @return void
     */
    public function addNote(string $note, string $source): void
    {
        $this->currentStep++;
        $this->history[] = [
            'step' => $this->currentStep,
            'action' => 'note',
            'note' => $note,
            'source' => $source,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}
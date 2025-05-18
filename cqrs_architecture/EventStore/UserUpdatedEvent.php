<?php

namespace CQRSArchitecture\EventStore;

/**
 * UserUpdatedEvent
 * 
 * This class represents an event that occurs when a user is updated.
 */
class UserUpdatedEvent extends Event
{
    /**
     * @var int The ID of the user
     */
    private int $userId;
    
    /**
     * @var array The changes made to the user
     */
    private array $changes;
    
    /**
     * Constructor
     * 
     * @param int $userId The ID of the user
     * @param array $changes The changes made to the user
     */
    public function __construct(int $userId, array $changes)
    {
        parent::__construct('user_updated');
        $this->userId = $userId;
        $this->changes = $changes;
    }
    
    /**
     * Get the ID of the user
     * 
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
    
    /**
     * Get the changes made to the user
     * 
     * @return array
     */
    public function getChanges(): array
    {
        return $this->changes;
    }
    
    /**
     * Check if a specific field was changed
     * 
     * @param string $field The field to check
     * @return bool
     */
    public function hasChange(string $field): bool
    {
        return isset($this->changes[$field]);
    }
    
    /**
     * Get the new value of a specific field
     * 
     * @param string $field The field to get
     * @param mixed $default The default value if the field wasn't changed
     * @return mixed
     */
    public function getChange(string $field, $default = null)
    {
        return $this->changes[$field] ?? $default;
    }
    
    /**
     * Get the data of the event
     * 
     * @return array
     */
    public function getData(): array
    {
        return [
            'user_id' => $this->userId,
            'changes' => $this->changes
        ];
    }
}
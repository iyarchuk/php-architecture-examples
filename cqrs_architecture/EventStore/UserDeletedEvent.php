<?php

namespace CQRSArchitecture\EventStore;

/**
 * UserDeletedEvent
 * 
 * This class represents an event that occurs when a user is deleted.
 */
class UserDeletedEvent extends Event
{
    /**
     * @var int The ID of the user
     */
    private int $userId;
    
    /**
     * Constructor
     * 
     * @param int $userId The ID of the user
     */
    public function __construct(int $userId)
    {
        parent::__construct('user_deleted');
        $this->userId = $userId;
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
     * Get the data of the event
     * 
     * @return array
     */
    public function getData(): array
    {
        return [
            'user_id' => $this->userId
        ];
    }
}
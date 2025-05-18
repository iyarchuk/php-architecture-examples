<?php

namespace CQRSArchitecture\Commands;

/**
 * DeleteUserCommand
 * 
 * This class represents a command to delete an existing user.
 * Commands are immutable and contain all the data needed to perform the operation.
 */
class DeleteUserCommand
{
    /**
     * @var int The ID of the user to delete
     */
    private int $userId;
    
    /**
     * Constructor
     * 
     * @param int $userId The ID of the user to delete
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }
    
    /**
     * Get the ID of the user to delete
     * 
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
}
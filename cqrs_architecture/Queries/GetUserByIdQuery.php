<?php

namespace CQRSArchitecture\Queries;

/**
 * GetUserByIdQuery
 * 
 * This class represents a query to retrieve a user by ID.
 * Queries are immutable and do not change the state of the system.
 */
class GetUserByIdQuery
{
    /**
     * @var int The ID of the user to retrieve
     */
    private int $userId;
    
    /**
     * Constructor
     * 
     * @param int $userId The ID of the user to retrieve
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }
    
    /**
     * Get the ID of the user to retrieve
     * 
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
}
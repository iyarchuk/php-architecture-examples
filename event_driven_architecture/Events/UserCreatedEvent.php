<?php

namespace EventDrivenArchitecture\Events;

/**
 * UserCreatedEvent
 * 
 * This class represents an event that occurs when a user is created.
 */
class UserCreatedEvent extends Event
{
    /**
     * @var int The ID of the created user
     */
    private int $userId;
    
    /**
     * @var string The name of the created user
     */
    private string $userName;
    
    /**
     * @var string The email of the created user
     */
    private string $userEmail;
    
    /**
     * Constructor
     * 
     * @param int $userId The ID of the created user
     * @param string $userName The name of the created user
     * @param string $userEmail The email of the created user
     */
    public function __construct(int $userId, string $userName, string $userEmail)
    {
        parent::__construct('user.created');
        $this->userId = $userId;
        $this->userName = $userName;
        $this->userEmail = $userEmail;
    }
    
    /**
     * Get the ID of the created user
     * 
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
    
    /**
     * Get the name of the created user
     * 
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }
    
    /**
     * Get the email of the created user
     * 
     * @return string
     */
    public function getUserEmail(): string
    {
        return $this->userEmail;
    }
    
    /**
     * Get the data associated with the event
     * 
     * @return array
     */
    public function getData(): array
    {
        return [
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'user_email' => $this->userEmail,
        ];
    }
}
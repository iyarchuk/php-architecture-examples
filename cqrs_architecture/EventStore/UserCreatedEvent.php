<?php

namespace CQRSArchitecture\EventStore;

/**
 * UserCreatedEvent
 * 
 * This class represents an event that occurs when a user is created.
 */
class UserCreatedEvent extends Event
{
    /**
     * @var int The ID of the user
     */
    private int $userId;
    
    /**
     * @var string The name of the user
     */
    private string $name;
    
    /**
     * @var string The email of the user
     */
    private string $email;
    
    /**
     * @var string The date the user was created
     */
    private string $createdAt;
    
    /**
     * Constructor
     * 
     * @param int $userId The ID of the user
     * @param string $name The name of the user
     * @param string $email The email of the user
     * @param string $createdAt The date the user was created
     */
    public function __construct(int $userId, string $name, string $email, string $createdAt)
    {
        parent::__construct('user_created');
        $this->userId = $userId;
        $this->name = $name;
        $this->email = $email;
        $this->createdAt = $createdAt;
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
     * Get the name of the user
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Get the email of the user
     * 
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
    
    /**
     * Get the date the user was created
     * 
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
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
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->createdAt
        ];
    }
}
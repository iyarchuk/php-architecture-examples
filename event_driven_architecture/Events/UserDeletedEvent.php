<?php

namespace EventDrivenArchitecture\Events;

/**
 * UserDeletedEvent
 * 
 * This class represents an event that occurs when a user is deleted.
 */
class UserDeletedEvent extends Event
{
    /**
     * @var int The ID of the deleted user
     */
    private int $userId;
    
    /**
     * @var string The name of the deleted user
     */
    private string $userName;
    
    /**
     * @var string The email of the deleted user
     */
    private string $userEmail;
    
    /**
     * @var string The reason for deletion (if provided)
     */
    private ?string $reason;
    
    /**
     * Constructor
     * 
     * @param int $userId The ID of the deleted user
     * @param string $userName The name of the deleted user
     * @param string $userEmail The email of the deleted user
     * @param string|null $reason The reason for deletion (if provided)
     */
    public function __construct(int $userId, string $userName, string $userEmail, ?string $reason = null)
    {
        parent::__construct('user.deleted');
        $this->userId = $userId;
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->reason = $reason;
    }
    
    /**
     * Get the ID of the deleted user
     * 
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
    
    /**
     * Get the name of the deleted user
     * 
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }
    
    /**
     * Get the email of the deleted user
     * 
     * @return string
     */
    public function getUserEmail(): string
    {
        return $this->userEmail;
    }
    
    /**
     * Get the reason for deletion
     * 
     * @return string|null
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }
    
    /**
     * Check if a reason was provided
     * 
     * @return bool
     */
    public function hasReason(): bool
    {
        return $this->reason !== null;
    }
    
    /**
     * Get the data associated with the event
     * 
     * @return array
     */
    public function getData(): array
    {
        $data = [
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'user_email' => $this->userEmail,
        ];
        
        if ($this->reason !== null) {
            $data['reason'] = $this->reason;
        }
        
        return $data;
    }
}
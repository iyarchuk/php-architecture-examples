<?php

namespace EventDrivenArchitecture\Events;

/**
 * UserUpdatedEvent
 * 
 * This class represents an event that occurs when a user is updated.
 */
class UserUpdatedEvent extends Event
{
    /**
     * @var int The ID of the updated user
     */
    private int $userId;
    
    /**
     * @var string The name of the updated user
     */
    private string $userName;
    
    /**
     * @var string The email of the updated user
     */
    private string $userEmail;
    
    /**
     * @var array The fields that were updated
     */
    private array $updatedFields;
    
    /**
     * Constructor
     * 
     * @param int $userId The ID of the updated user
     * @param string $userName The name of the updated user
     * @param string $userEmail The email of the updated user
     * @param array $updatedFields The fields that were updated
     */
    public function __construct(int $userId, string $userName, string $userEmail, array $updatedFields)
    {
        parent::__construct('user.updated');
        $this->userId = $userId;
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->updatedFields = $updatedFields;
    }
    
    /**
     * Get the ID of the updated user
     * 
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
    
    /**
     * Get the name of the updated user
     * 
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }
    
    /**
     * Get the email of the updated user
     * 
     * @return string
     */
    public function getUserEmail(): string
    {
        return $this->userEmail;
    }
    
    /**
     * Get the fields that were updated
     * 
     * @return array
     */
    public function getUpdatedFields(): array
    {
        return $this->updatedFields;
    }
    
    /**
     * Check if a specific field was updated
     * 
     * @param string $field The field to check
     * @return bool
     */
    public function wasFieldUpdated(string $field): bool
    {
        return in_array($field, $this->updatedFields);
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
            'updated_fields' => $this->updatedFields,
        ];
    }
}
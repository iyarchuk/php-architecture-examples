<?php

namespace CQRSArchitecture\Commands;

/**
 * UpdateUserCommand
 * 
 * This class represents a command to update an existing user.
 * Commands are immutable and contain all the data needed to perform the operation.
 */
class UpdateUserCommand
{
    /**
     * @var int The ID of the user to update
     */
    private int $userId;
    
    /**
     * @var string|null The new name of the user (null if not changing)
     */
    private ?string $name;
    
    /**
     * @var string|null The new email of the user (null if not changing)
     */
    private ?string $email;
    
    /**
     * @var string|null The new password of the user (null if not changing)
     */
    private ?string $password;
    
    /**
     * Constructor
     * 
     * @param int $userId The ID of the user to update
     * @param string|null $name The new name of the user (null if not changing)
     * @param string|null $email The new email of the user (null if not changing)
     * @param string|null $password The new password of the user (null if not changing)
     */
    public function __construct(int $userId, ?string $name = null, ?string $email = null, ?string $password = null)
    {
        $this->userId = $userId;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }
    
    /**
     * Get the ID of the user to update
     * 
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
    
    /**
     * Get the new name of the user
     * 
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
    
    /**
     * Get the new email of the user
     * 
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }
    
    /**
     * Get the new password of the user
     * 
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }
    
    /**
     * Check if the name is being updated
     * 
     * @return bool
     */
    public function hasNameUpdate(): bool
    {
        return $this->name !== null;
    }
    
    /**
     * Check if the email is being updated
     * 
     * @return bool
     */
    public function hasEmailUpdate(): bool
    {
        return $this->email !== null;
    }
    
    /**
     * Check if the password is being updated
     * 
     * @return bool
     */
    public function hasPasswordUpdate(): bool
    {
        return $this->password !== null;
    }
}
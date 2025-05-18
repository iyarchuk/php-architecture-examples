<?php

namespace CQRSArchitecture\Models;

use CQRSArchitecture\EventStore\UserCreatedEvent;
use CQRSArchitecture\EventStore\UserUpdatedEvent;
use CQRSArchitecture\EventStore\UserDeletedEvent;

/**
 * User
 * 
 * This class represents the domain model for a user.
 * It encapsulates the business rules and validation for users.
 */
class User
{
    /**
     * @var int The ID of the user
     */
    private int $id;
    
    /**
     * @var string The name of the user
     */
    private string $name;
    
    /**
     * @var string The email of the user
     */
    private string $email;
    
    /**
     * @var string The hashed password of the user
     */
    private string $password;
    
    /**
     * @var string The date the user was created
     */
    private string $createdAt;
    
    /**
     * @var array The events that have occurred
     */
    private array $events = [];
    
    /**
     * Constructor
     * 
     * @param int $id The ID of the user
     * @param string $name The name of the user
     * @param string $email The email of the user
     * @param string $password The hashed password of the user
     * @param string $createdAt The date the user was created
     */
    public function __construct(int $id, string $name, string $email, string $password, string $createdAt)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt;
    }
    
    /**
     * Create a new user
     * 
     * @param int $id The ID of the user
     * @param string $name The name of the user
     * @param string $email The email of the user
     * @param string $password The password of the user (will be hashed)
     * @return User
     * @throws \InvalidArgumentException
     */
    public static function create(int $id, string $name, string $email, string $password): User
    {
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
        
        // Validate password
        if (strlen($password) < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters long');
        }
        
        // Create user
        $createdAt = date('Y-m-d H:i:s');
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $user = new self($id, $name, $email, $hashedPassword, $createdAt);
        
        // Record event
        $user->recordEvent(new UserCreatedEvent($id, $name, $email, $createdAt));
        
        return $user;
    }
    
    /**
     * Update the user
     * 
     * @param string|null $name The new name of the user (null if not changing)
     * @param string|null $email The new email of the user (null if not changing)
     * @param string|null $password The new password of the user (null if not changing)
     * @return void
     * @throws \InvalidArgumentException
     */
    public function update(?string $name = null, ?string $email = null, ?string $password = null): void
    {
        $changes = [];
        
        // Update name if provided
        if ($name !== null) {
            $this->name = $name;
            $changes['name'] = $name;
        }
        
        // Update email if provided
        if ($email !== null) {
            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException('Invalid email format');
            }
            
            $this->email = $email;
            $changes['email'] = $email;
        }
        
        // Update password if provided
        if ($password !== null) {
            // Validate password
            if (strlen($password) < 8) {
                throw new \InvalidArgumentException('Password must be at least 8 characters long');
            }
            
            $this->password = password_hash($password, PASSWORD_DEFAULT);
            $changes['password'] = true; // Don't include the actual password in the event
        }
        
        // Record event if there were changes
        if (!empty($changes)) {
            $this->recordEvent(new UserUpdatedEvent($this->id, $changes));
        }
    }
    
    /**
     * Mark the user as deleted
     * 
     * @return void
     */
    public function delete(): void
    {
        $this->recordEvent(new UserDeletedEvent($this->id));
    }
    
    /**
     * Record an event
     * 
     * @param object $event The event to record
     * @return void
     */
    private function recordEvent(object $event): void
    {
        $this->events[] = $event;
    }
    
    /**
     * Get the recorded events and clear them
     * 
     * @return array
     */
    public function getRecordedEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }
    
    /**
     * Get the ID of the user
     * 
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
     * Get the hashed password of the user
     * 
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
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
     * Verify a password
     * 
     * @param string $password The password to verify
     * @return bool
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}
<?php

namespace DomainDrivenDesign\Domain\ValueObjects;

/**
 * Email Value Object
 * 
 * This class represents an email value object in the domain.
 * In DDD, value objects are objects that have no conceptual identity and are defined by their attributes.
 */
class Email
{
    /**
     * @var string The email address
     */
    private string $email;
    
    /**
     * Constructor
     * 
     * @param string $email
     * @throws \InvalidArgumentException
     */
    public function __construct(string $email)
    {
        $this->setEmail($email);
    }
    
    /**
     * Set the email address
     * 
     * @param string $email
     * @throws \InvalidArgumentException
     */
    private function setEmail(string $email): void
    {
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
        
        // Validate email length
        if (strlen($email) > 255) {
            throw new \InvalidArgumentException('Email is too long (maximum 255 characters)');
        }
        
        $this->email = $email;
    }
    
    /**
     * Get the email address as a string
     * 
     * @return string
     */
    public function toString(): string
    {
        return $this->email;
    }
    
    /**
     * Get the domain part of the email
     * 
     * @return string
     */
    public function getDomain(): string
    {
        $parts = explode('@', $this->email);
        return $parts[1];
    }
    
    /**
     * Get the local part of the email (before the @)
     * 
     * @return string
     */
    public function getLocalPart(): string
    {
        $parts = explode('@', $this->email);
        return $parts[0];
    }
    
    /**
     * Check if this email equals another email
     * 
     * @param Email $other
     * @return bool
     */
    public function equals(Email $other): bool
    {
        return strtolower($this->email) === strtolower($other->email);
    }
    
    /**
     * Create an Email from a string
     * 
     * @param string $email
     * @return self
     */
    public static function fromString(string $email): self
    {
        return new self($email);
    }
    
    /**
     * String representation of the email
     * 
     * @return string
     */
    public function __toString(): string
    {
        return $this->email;
    }
}
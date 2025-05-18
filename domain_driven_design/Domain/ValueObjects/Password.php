<?php

namespace DomainDrivenDesign\Domain\ValueObjects;

/**
 * Password Value Object
 * 
 * This class represents a password value object in the domain.
 * In DDD, value objects are objects that have no conceptual identity and are defined by their attributes.
 */
class Password
{
    /**
     * @var string The hashed password
     */
    private string $hashedPassword;
    
    /**
     * Constructor
     * 
     * @param string $hashedPassword
     */
    private function __construct(string $hashedPassword)
    {
        $this->hashedPassword = $hashedPassword;
    }
    
    /**
     * Create a Password from a plain text password
     * 
     * @param string $plainPassword
     * @return self
     * @throws \InvalidArgumentException
     */
    public static function fromPlainText(string $plainPassword): self
    {
        // Validate password length
        if (strlen($plainPassword) < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters long');
        }
        
        // Validate password complexity
        if (!preg_match('/[A-Z]/', $plainPassword)) {
            throw new \InvalidArgumentException('Password must contain at least one uppercase letter');
        }
        
        if (!preg_match('/[a-z]/', $plainPassword)) {
            throw new \InvalidArgumentException('Password must contain at least one lowercase letter');
        }
        
        if (!preg_match('/[0-9]/', $plainPassword)) {
            throw new \InvalidArgumentException('Password must contain at least one number');
        }
        
        // Hash the password
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
        
        return new self($hashedPassword);
    }
    
    /**
     * Create a Password from an already hashed password
     * 
     * @param string $hashedPassword
     * @return self
     */
    public static function fromHash(string $hashedPassword): self
    {
        return new self($hashedPassword);
    }
    
    /**
     * Verify if the provided plain text password matches the hashed password
     * 
     * @param string $plainPassword
     * @return bool
     */
    public function verify(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->hashedPassword);
    }
    
    /**
     * Get the hashed password
     * 
     * @return string
     */
    public function getHash(): string
    {
        return $this->hashedPassword;
    }
    
    /**
     * Check if this password needs rehashing
     * 
     * @return bool
     */
    public function needsRehash(): bool
    {
        return password_needs_rehash($this->hashedPassword, PASSWORD_DEFAULT);
    }
    
    /**
     * Create a new password with the same plain text but a new hash
     * 
     * @param string $plainPassword
     * @return self
     */
    public function rehash(string $plainPassword): self
    {
        return self::fromPlainText($plainPassword);
    }
}
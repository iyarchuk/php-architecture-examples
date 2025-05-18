<?php

namespace DomainDrivenDesign\Domain\ValueObjects;

/**
 * UserId Value Object
 * 
 * This class represents a user ID value object in the domain.
 * In DDD, value objects are objects that have no conceptual identity and are defined by their attributes.
 */
class UserId
{
    /**
     * @var string The unique identifier
     */
    private string $id;
    
    /**
     * Constructor
     * 
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }
    
    /**
     * Get the ID as a string
     * 
     * @return string
     */
    public function toString(): string
    {
        return $this->id;
    }
    
    /**
     * Check if this ID equals another ID
     * 
     * @param UserId $other
     * @return bool
     */
    public function equals(UserId $other): bool
    {
        return $this->id === $other->id;
    }
    
    /**
     * Generate a new unique ID
     * 
     * @return self
     */
    public static function generate(): self
    {
        return new self(self::generateUuid());
    }
    
    /**
     * Generate a UUID v4
     * 
     * @return string
     */
    private static function generateUuid(): string
    {
        // Generate 16 bytes (128 bits) of random data
        $data = random_bytes(16);
        
        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        
        // Output the 36 character UUID
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    
    /**
     * Create a UserId from a string
     * 
     * @param string $id
     * @return self
     */
    public static function fromString(string $id): self
    {
        return new self($id);
    }
    
    /**
     * String representation of the ID
     * 
     * @return string
     */
    public function __toString(): string
    {
        return $this->id;
    }
}
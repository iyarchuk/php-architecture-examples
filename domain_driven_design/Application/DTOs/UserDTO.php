<?php

namespace DomainDrivenDesign\Application\DTOs;

/**
 * UserDTO
 * 
 * This class represents a Data Transfer Object (DTO) for user data.
 * In DDD, DTOs are used to transfer data between layers without exposing domain objects.
 */
class UserDTO
{
    /**
     * @var string The user ID
     */
    private string $id;
    
    /**
     * @var string The user name
     */
    private string $name;
    
    /**
     * @var string The user email
     */
    private string $email;
    
    /**
     * @var string The date the user was created
     */
    private string $createdAt;
    
    /**
     * @var array The user profile
     */
    private array $profile;
    
    /**
     * @var array The user roles
     */
    private array $roles;
    
    /**
     * @var array The user preferences
     */
    private array $preferences;
    
    /**
     * Constructor
     * 
     * @param string $id
     * @param string $name
     * @param string $email
     * @param string $createdAt
     * @param array $profile
     * @param array $roles
     * @param array $preferences
     */
    public function __construct(
        string $id,
        string $name,
        string $email,
        string $createdAt,
        array $profile = [],
        array $roles = [],
        array $preferences = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->createdAt = $createdAt;
        $this->profile = $profile;
        $this->roles = $roles;
        $this->preferences = $preferences;
    }
    
    /**
     * Get the user ID
     * 
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
    
    /**
     * Get the user name
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Get the user email
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
     * Get the user profile
     * 
     * @return array
     */
    public function getProfile(): array
    {
        return $this->profile;
    }
    
    /**
     * Get a profile field
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getProfileField(string $key, $default = null)
    {
        return $this->profile[$key] ?? $default;
    }
    
    /**
     * Get the user roles
     * 
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
    
    /**
     * Check if the user has a role
     * 
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }
    
    /**
     * Get the user preferences
     * 
     * @return array
     */
    public function getPreferences(): array
    {
        return $this->preferences;
    }
    
    /**
     * Get a preference
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getPreference(string $key, $default = null)
    {
        return $this->preferences[$key] ?? $default;
    }
    
    /**
     * Convert the DTO to an array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->createdAt,
            'profile' => $this->profile,
            'roles' => $this->roles,
            'preferences' => $this->preferences
        ];
    }
}
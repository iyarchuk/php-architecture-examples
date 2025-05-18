<?php

namespace DomainDrivenDesign\Domain\Aggregates;

use DomainDrivenDesign\Domain\Entities\User;
use DomainDrivenDesign\Domain\ValueObjects\Email;
use DomainDrivenDesign\Domain\ValueObjects\Password;
use DomainDrivenDesign\Domain\ValueObjects\UserId;

/**
 * UserAggregate
 * 
 * This class represents a user aggregate root in the domain.
 * In DDD, aggregates are clusters of domain objects that can be treated as a single unit.
 * An aggregate root is the entry point for any external references to objects within the aggregate.
 */
class UserAggregate
{
    /**
     * @var User The user entity (aggregate root)
     */
    private User $user;
    
    /**
     * @var array User's profile information
     */
    private array $profile = [];
    
    /**
     * @var array User's roles
     */
    private array $roles = [];
    
    /**
     * @var array User's preferences
     */
    private array $preferences = [];
    
    /**
     * Constructor
     * 
     * @param User $user
     */
    private function __construct(User $user)
    {
        $this->user = $user;
    }
    
    /**
     * Create a new user aggregate
     * 
     * @param string $name
     * @param string $email
     * @param string $password
     * @return self
     */
    public static function create(string $name, string $email, string $password): self
    {
        $user = User::create($name, $email, $password);
        return new self($user);
    }
    
    /**
     * Reconstitute a user aggregate from existing data
     * 
     * @param UserId $id
     * @param string $name
     * @param Email $email
     * @param Password $password
     * @param \DateTimeImmutable $createdAt
     * @param array $profile
     * @param array $roles
     * @param array $preferences
     * @return self
     */
    public static function reconstitute(
        UserId $id,
        string $name,
        Email $email,
        Password $password,
        \DateTimeImmutable $createdAt,
        array $profile = [],
        array $roles = [],
        array $preferences = []
    ): self {
        $user = new User($id, $name, $email, $password, $createdAt);
        $aggregate = new self($user);
        $aggregate->profile = $profile;
        $aggregate->roles = $roles;
        $aggregate->preferences = $preferences;
        
        return $aggregate;
    }
    
    /**
     * Get the user entity
     * 
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
    
    /**
     * Get the user ID
     * 
     * @return UserId
     */
    public function getId(): UserId
    {
        return $this->user->getId();
    }
    
    /**
     * Change the user's email
     * 
     * @param Email $email
     */
    public function changeEmail(Email $email): void
    {
        $this->user->changeEmail($email);
    }
    
    /**
     * Change the user's password
     * 
     * @param Password $password
     */
    public function changePassword(Password $password): void
    {
        $this->user->changePassword($password);
    }
    
    /**
     * Add a profile field
     * 
     * @param string $key
     * @param mixed $value
     */
    public function addProfileField(string $key, $value): void
    {
        $this->profile[$key] = $value;
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
     * Get all profile fields
     * 
     * @return array
     */
    public function getProfile(): array
    {
        return $this->profile;
    }
    
    /**
     * Add a role
     * 
     * @param string $role
     */
    public function addRole(string $role): void
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }
    
    /**
     * Remove a role
     * 
     * @param string $role
     */
    public function removeRole(string $role): void
    {
        $key = array_search($role, $this->roles);
        if ($key !== false) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles); // Reindex array
        }
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
     * Get all roles
     * 
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
    
    /**
     * Set a preference
     * 
     * @param string $key
     * @param mixed $value
     */
    public function setPreference(string $key, $value): void
    {
        $this->preferences[$key] = $value;
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
     * Get all preferences
     * 
     * @return array
     */
    public function getPreferences(): array
    {
        return $this->preferences;
    }
}
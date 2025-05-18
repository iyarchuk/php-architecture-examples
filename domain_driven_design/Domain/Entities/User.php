<?php

namespace DomainDrivenDesign\Domain\Entities;

use DomainDrivenDesign\Domain\ValueObjects\Email;
use DomainDrivenDesign\Domain\ValueObjects\UserId;
use DomainDrivenDesign\Domain\ValueObjects\Password;

/**
 * User Entity
 * 
 * This class represents a user entity in the domain.
 * In DDD, entities are objects that have a distinct identity that runs through time and different states.
 */
class User
{
    /**
     * @var UserId The unique identifier of the user
     */
    private UserId $id;
    
    /**
     * @var string The name of the user
     */
    private string $name;
    
    /**
     * @var Email The email of the user
     */
    private Email $email;
    
    /**
     * @var Password The password of the user
     */
    private Password $password;
    
    /**
     * @var \DateTimeImmutable The date the user was created
     */
    private \DateTimeImmutable $createdAt;
    
    /**
     * Constructor
     * 
     * @param UserId $id
     * @param string $name
     * @param Email $email
     * @param Password $password
     * @param \DateTimeImmutable|null $createdAt
     */
    public function __construct(
        UserId $id,
        string $name,
        Email $email,
        Password $password,
        ?\DateTimeImmutable $createdAt = null
    ) {
        $this->id = $id;
        $this->setName($name);
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
    }
    
    /**
     * Get the user ID
     * 
     * @return UserId
     */
    public function getId(): UserId
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
     * Set the user name
     * 
     * @param string $name
     * @throws \InvalidArgumentException
     */
    public function setName(string $name): void
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('User name cannot be empty');
        }
        
        if (strlen($name) < 2) {
            throw new \InvalidArgumentException('User name must be at least 2 characters long');
        }
        
        if (strlen($name) > 100) {
            throw new \InvalidArgumentException('User name cannot be longer than 100 characters');
        }
        
        $this->name = $name;
    }
    
    /**
     * Get the user email
     * 
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }
    
    /**
     * Change the user email
     * 
     * @param Email $email
     */
    public function changeEmail(Email $email): void
    {
        $this->email = $email;
    }
    
    /**
     * Get the user password
     * 
     * @return Password
     */
    public function getPassword(): Password
    {
        return $this->password;
    }
    
    /**
     * Change the user password
     * 
     * @param Password $password
     */
    public function changePassword(Password $password): void
    {
        $this->password = $password;
    }
    
    /**
     * Get the date the user was created
     * 
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
    
    /**
     * Verify if the provided password matches the user's password
     * 
     * @param string $plainPassword
     * @return bool
     */
    public function verifyPassword(string $plainPassword): bool
    {
        return $this->password->verify($plainPassword);
    }
    
    /**
     * Create a new user
     * 
     * This is a factory method that creates a new user with a new ID.
     * 
     * @param string $name
     * @param string $email
     * @param string $password
     * @return self
     */
    public static function create(string $name, string $email, string $password): self
    {
        return new self(
            UserId::generate(),
            $name,
            new Email($email),
            Password::fromPlainText($password)
        );
    }
}
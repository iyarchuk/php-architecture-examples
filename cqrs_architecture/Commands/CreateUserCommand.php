<?php

namespace CQRSArchitecture\Commands;

/**
 * CreateUserCommand
 * 
 * This class represents a command to create a new user.
 * Commands are immutable and contain all the data needed to perform the operation.
 */
class CreateUserCommand
{
    /**
     * @var string The name of the user
     */
    private string $name;
    
    /**
     * @var string The email of the user
     */
    private string $email;
    
    /**
     * @var string The password of the user
     */
    private string $password;
    
    /**
     * Constructor
     * 
     * @param string $name The name of the user
     * @param string $email The email of the user
     * @param string $password The password of the user
     */
    public function __construct(string $name, string $email, string $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
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
     * Get the password of the user
     * 
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
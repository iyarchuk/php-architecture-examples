<?php

namespace OnionArchitecture\Application;

/**
 * CreateUserRequest in the Application layer
 * Contains data for creating a user
 */
class CreateUserRequest
{
    private $name;
    private $email;
    private $password;

    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function isValid(): bool
    {
        // Simple validation
        if (empty($this->name) || empty($this->email) || empty($this->password)) {
            return false;
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }
}
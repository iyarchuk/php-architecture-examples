<?php

namespace OnionArchitecture\Domain;

/**
 * User entity in the Domain layer (Core)
 * This is the innermost layer of the Onion Architecture
 */
class User
{
    private $id;
    private $name;
    private $email;
    private $password;

    public function __construct(string $name, string $email, string $password)
    {
        $this->id = uniqid();
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public function getId(): string
    {
        return $this->id;
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

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email
        ];
    }
}
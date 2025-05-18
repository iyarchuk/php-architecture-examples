<?php

namespace LayeredArchitecture\Model;

/**
 * User Model
 * 
 * This class represents a user entity in the system.
 * It's a shared model that can be used by all layers.
 */
class User
{
    private ?int $id = null;
    private string $name;
    private string $email;
    private string $password;
    private \DateTime $createdAt;

    public function __construct(string $name, string $email, string $password)
    {
        $this->name = $name;
        $this->setEmail($email);
        $this->setPassword($password);
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Sets the email and validates it
     * 
     * @param string $email
     * @throws \InvalidArgumentException
     */
    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Sets the password and validates it
     * 
     * @param string $password
     * @throws \InvalidArgumentException
     */
    public function setPassword(string $password): void
    {
        if (strlen($password) < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters long');
        }
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * Verifies if the provided password matches the stored password
     * 
     * @param string $password
     * @return bool
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}
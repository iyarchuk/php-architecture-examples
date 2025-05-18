<?php

namespace OnionArchitecture\Application;

/**
 * GetUserRequest in the Application layer
 * Contains data for retrieving a user
 */
class GetUserRequest
{
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isValid(): bool
    {
        return !empty($this->id);
    }
}
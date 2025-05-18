<?php

namespace OnionArchitecture\Presentation;

use OnionArchitecture\Application\CreateUserRequest;
use OnionArchitecture\Application\GetUserRequest;
use OnionArchitecture\Application\UserApplicationService;

/**
 * UserController in the Presentation layer
 * Handles HTTP requests and responses
 * Uses the UserApplicationService from the Application layer
 */
class UserController
{
    private $userApplicationService;

    public function __construct(UserApplicationService $userApplicationService)
    {
        $this->userApplicationService = $userApplicationService;
    }

    /**
     * Create a new user
     *
     * @param array $data The data for creating a user
     * @return array Result of the operation
     */
    public function createUser(array $data): array
    {
        $request = new CreateUserRequest($data);
        return $this->userApplicationService->createUser($request);
    }

    /**
     * Get a user by ID
     *
     * @param string $id The ID of the user to get
     * @return array Result of the operation
     */
    public function getUser(string $id): array
    {
        $request = new GetUserRequest($id);
        return $this->userApplicationService->getUser($request);
    }
}
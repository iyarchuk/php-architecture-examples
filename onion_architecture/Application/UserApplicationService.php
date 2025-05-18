<?php

namespace OnionArchitecture\Application;

use OnionArchitecture\Domain\UserService;

/**
 * UserApplicationService in the Application layer
 * Coordinates the flow of data to and from the Domain layer
 * Acts as a facade for the Domain layer
 */
class UserApplicationService
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Create a new user
     *
     * @param CreateUserRequest $request The request containing user data
     * @return array Result of the operation
     */
    public function createUser(CreateUserRequest $request): array
    {
        // Validate request
        if (!$request->isValid()) {
            return [
                'success' => false,
                'message' => 'Invalid request data'
            ];
        }

        // Delegate to domain service
        return $this->userService->createUser(
            $request->getName(),
            $request->getEmail(),
            $request->getPassword()
        );
    }

    /**
     * Get a user by ID
     *
     * @param GetUserRequest $request The request containing the user ID
     * @return array Result of the operation
     */
    public function getUser(GetUserRequest $request): array
    {
        // Validate request
        if (!$request->isValid()) {
            return [
                'success' => false,
                'message' => 'Invalid request data'
            ];
        }

        // Delegate to domain service
        return $this->userService->getUserById($request->getId());
    }
}
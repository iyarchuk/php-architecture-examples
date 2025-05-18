<?php

namespace CleanArchitecture\Application;

use CleanArchitecture\Domain\User;
use CleanArchitecture\Domain\UserRepositoryInterface;

/**
 * Get User Use Case
 * 
 * This class represents a use case for retrieving a user by ID.
 * It belongs to the application layer and orchestrates the flow of data
 * between the domain entities and the infrastructure.
 */
class GetUserUseCase
{
    private UserRepositoryInterface $userRepository;

    /**
     * Constructor
     * 
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Execute the use case
     * 
     * @param GetUserRequest $request
     * @return GetUserResponse
     */
    public function execute(GetUserRequest $request): GetUserResponse
    {
        // Find the user by ID
        $user = $this->userRepository->findById($request->userId);
        
        if ($user === null) {
            // Return a failed response if user not found
            return new GetUserResponse(false, null, 'User not found');
        }
        
        // Return a successful response with the user
        return new GetUserResponse(true, $user);
    }
}

/**
 * Get User Request DTO
 * 
 * This class represents the input data for the GetUserUseCase.
 */
class GetUserRequest
{
    public int $userId;

    /**
     * Constructor
     * 
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }
}

/**
 * Get User Response DTO
 * 
 * This class represents the output data from the GetUserUseCase.
 */
class GetUserResponse
{
    public bool $success;
    public ?User $user;
    public ?string $errorMessage;

    /**
     * Constructor
     * 
     * @param bool $success
     * @param User|null $user
     * @param string|null $errorMessage
     */
    public function __construct(bool $success, ?User $user, ?string $errorMessage = null)
    {
        $this->success = $success;
        $this->user = $user;
        $this->errorMessage = $errorMessage;
    }
}
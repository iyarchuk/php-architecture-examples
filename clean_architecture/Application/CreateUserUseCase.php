<?php

namespace CleanArchitecture\Application;

use CleanArchitecture\Domain\User;
use CleanArchitecture\Domain\UserRepositoryInterface;

/**
 * Create User Use Case
 * 
 * This class represents a use case for creating a new user.
 * It belongs to the application layer and orchestrates the flow of data
 * between the domain entities and the infrastructure.
 */
class CreateUserUseCase
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
     * @param CreateUserRequest $request
     * @return CreateUserResponse
     * @throws \InvalidArgumentException
     */
    public function execute(CreateUserRequest $request): CreateUserResponse
    {
        // Check if user with the same email already exists
        $existingUser = $this->userRepository->findByEmail($request->email);
        if ($existingUser !== null) {
            return new CreateUserResponse(false, null, 'User with this email already exists');
        }

        try {
            // Create a new user entity
            $user = new User($request->name, $request->email, $request->password);
            
            // Save the user to the repository
            $savedUser = $this->userRepository->save($user);
            
            // Return a successful response
            return new CreateUserResponse(true, $savedUser);
        } catch (\InvalidArgumentException $e) {
            // Return a failed response with the error message
            return new CreateUserResponse(false, null, $e->getMessage());
        }
    }
}

/**
 * Create User Request DTO
 * 
 * This class represents the input data for the CreateUserUseCase.
 */
class CreateUserRequest
{
    public string $name;
    public string $email;
    public string $password;

    /**
     * Constructor
     * 
     * @param string $name
     * @param string $email
     * @param string $password
     */
    public function __construct(string $name, string $email, string $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }
}

/**
 * Create User Response DTO
 * 
 * This class represents the output data from the CreateUserUseCase.
 */
class CreateUserResponse
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
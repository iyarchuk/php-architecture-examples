<?php

namespace DomainDrivenDesign\Application\Services;

use DomainDrivenDesign\Domain\Aggregates\UserAggregate;
use DomainDrivenDesign\Domain\Entities\User;
use DomainDrivenDesign\Domain\Services\UserService;
use DomainDrivenDesign\Domain\ValueObjects\Email;
use DomainDrivenDesign\Domain\ValueObjects\Password;
use DomainDrivenDesign\Domain\ValueObjects\UserId;
use DomainDrivenDesign\Application\DTOs\UserDTO;

/**
 * UserApplicationService
 * 
 * This class represents an application service for user-related operations.
 * In DDD, application services orchestrate the execution of domain logic and manage transaction boundaries.
 */
class UserApplicationService
{
    /**
     * @var UserService The domain service for user-related operations
     */
    private UserService $userService;
    
    /**
     * Constructor
     * 
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    
    /**
     * Register a new user
     * 
     * @param string $name
     * @param string $email
     * @param string $password
     * @return UserDTO
     * @throws \InvalidArgumentException
     */
    public function registerUser(string $name, string $email, string $password): UserDTO
    {
        // Create Email value object
        $emailObj = new Email($email);
        
        // Check if email is already in use
        if ($this->userService->isEmailAlreadyInUse($emailObj)) {
            throw new \InvalidArgumentException('Email is already in use');
        }
        
        // Create user aggregate
        $userAggregate = UserAggregate::create($name, $email, $password);
        
        // Add default role
        $userAggregate->addRole('user');
        
        // Add user to repository
        $this->userService->addUser($userAggregate->getUser());
        
        // Return DTO
        return $this->createUserDTO($userAggregate);
    }
    
    /**
     * Get a user by ID
     * 
     * @param string $userId
     * @return UserDTO|null
     */
    public function getUserById(string $userId): ?UserDTO
    {
        $user = $this->userService->getUserById(UserId::fromString($userId));
        
        if (!$user) {
            return null;
        }
        
        // Reconstitute user aggregate
        $userAggregate = $this->reconstitute($user);
        
        // Return DTO
        return $this->createUserDTO($userAggregate);
    }
    
    /**
     * Get a user by email
     * 
     * @param string $email
     * @return UserDTO|null
     */
    public function getUserByEmail(string $email): ?UserDTO
    {
        $user = $this->userService->getUserByEmail(new Email($email));
        
        if (!$user) {
            return null;
        }
        
        // Reconstitute user aggregate
        $userAggregate = $this->reconstitute($user);
        
        // Return DTO
        return $this->createUserDTO($userAggregate);
    }
    
    /**
     * Get all users
     * 
     * @return array
     */
    public function getAllUsers(): array
    {
        $users = $this->userService->getAllUsers();
        $userDTOs = [];
        
        foreach ($users as $user) {
            // Reconstitute user aggregate
            $userAggregate = $this->reconstitute($user);
            
            // Create DTO
            $userDTOs[] = $this->createUserDTO($userAggregate);
        }
        
        return $userDTOs;
    }
    
    /**
     * Update a user
     * 
     * @param string $userId
     * @param array $data
     * @return UserDTO|null
     * @throws \InvalidArgumentException
     */
    public function updateUser(string $userId, array $data): ?UserDTO
    {
        $user = $this->userService->getUserById(UserId::fromString($userId));
        
        if (!$user) {
            return null;
        }
        
        // Reconstitute user aggregate
        $userAggregate = $this->reconstitute($user);
        
        // Update name if provided
        if (isset($data['name'])) {
            $user->setName($data['name']);
        }
        
        // Update email if provided
        if (isset($data['email'])) {
            $emailObj = new Email($data['email']);
            
            // Check if email is already in use by another user
            if ($this->userService->isEmailAlreadyInUse($emailObj, $user->getId())) {
                throw new \InvalidArgumentException('Email is already in use');
            }
            
            $userAggregate->changeEmail($emailObj);
        }
        
        // Update password if provided
        if (isset($data['password'])) {
            $passwordObj = Password::fromPlainText($data['password']);
            $userAggregate->changePassword($passwordObj);
        }
        
        // Update profile if provided
        if (isset($data['profile']) && is_array($data['profile'])) {
            foreach ($data['profile'] as $key => $value) {
                $userAggregate->addProfileField($key, $value);
            }
        }
        
        // Update roles if provided
        if (isset($data['roles']) && is_array($data['roles'])) {
            foreach ($data['roles'] as $role) {
                $userAggregate->addRole($role);
            }
        }
        
        // Update preferences if provided
        if (isset($data['preferences']) && is_array($data['preferences'])) {
            foreach ($data['preferences'] as $key => $value) {
                $userAggregate->setPreference($key, $value);
            }
        }
        
        // Return DTO
        return $this->createUserDTO($userAggregate);
    }
    
    /**
     * Delete a user
     * 
     * @param string $userId
     * @return bool
     */
    public function deleteUser(string $userId): bool
    {
        return $this->userService->removeUser(UserId::fromString($userId));
    }
    
    /**
     * Authenticate a user
     * 
     * @param string $email
     * @param string $password
     * @return UserDTO|null
     */
    public function authenticateUser(string $email, string $password): ?UserDTO
    {
        $user = $this->userService->getUserByEmail(new Email($email));
        
        if (!$user || !$user->verifyPassword($password)) {
            return null;
        }
        
        // Reconstitute user aggregate
        $userAggregate = $this->reconstitute($user);
        
        // Return DTO
        return $this->createUserDTO($userAggregate);
    }
    
    /**
     * Reconstitute a user aggregate from a user entity
     * 
     * @param User $user
     * @return UserAggregate
     */
    private function reconstitute(User $user): UserAggregate
    {
        // In a real application, we would load the profile, roles, and preferences from a repository
        // For this example, we'll create an empty aggregate
        return UserAggregate::reconstitute(
            $user->getId(),
            $user->getName(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getCreatedAt()
        );
    }
    
    /**
     * Create a user DTO from a user aggregate
     * 
     * @param UserAggregate $userAggregate
     * @return UserDTO
     */
    private function createUserDTO(UserAggregate $userAggregate): UserDTO
    {
        $user = $userAggregate->getUser();
        
        return new UserDTO(
            $user->getId()->toString(),
            $user->getName(),
            $user->getEmail()->toString(),
            $user->getCreatedAt()->format('Y-m-d H:i:s'),
            $userAggregate->getProfile(),
            $userAggregate->getRoles(),
            $userAggregate->getPreferences()
        );
    }
}
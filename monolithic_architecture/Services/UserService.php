<?php

namespace MonolithicArchitecture\Services;

use MonolithicArchitecture\Models\User;
use MonolithicArchitecture\Utils\Logger;
use MonolithicArchitecture\Utils\Validator;
use MonolithicArchitecture\Utils\EmailSender;

/**
 * UserService contains business logic for user-related operations
 */
class UserService
{
    private $userModel;
    private $logger;
    private $validator;
    private $emailSender;

    public function __construct()
    {
        $this->userModel = new User();
        $this->logger = new Logger();
        $this->validator = new Validator();
        $this->emailSender = new EmailSender();
    }

    /**
     * Authenticate a user
     * 
     * @param string $email User email
     * @param string $password User password
     * @return array Response with user data and token
     */
    public function authenticate(string $email, string $password): array
    {
        // Log the authentication attempt
        $this->logger->info("Authentication attempt for email: $email");

        // Validate email format
        if (!$this->validator->isValidEmail($email)) {
            $this->logger->warning("Invalid email format: $email");
            return [
                'status' => 'error',
                'message' => 'Invalid email format'
            ];
        }

        // In a real application, we would check the credentials against the database
        // For this example, we'll simulate a successful authentication
        if ($email === 'john.doe@example.com' && $password === 'password123') {
            $user = [
                'id' => 1,
                'email' => $email,
                'name' => 'John Doe',
                'role' => 'user'
            ];

            // Generate a token
            $token = $this->generateToken($user['id']);

            $this->logger->info("User authenticated successfully: {$user['id']}");

            return [
                'status' => 'success',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ];
        }

        $this->logger->warning("Authentication failed for email: $email");

        return [
            'status' => 'error',
            'message' => 'Invalid credentials'
        ];
    }

    /**
     * Get user profile
     * 
     * @param int $userId User ID
     * @return array Response with user profile data
     */
    public function getProfile(int $userId): array
    {
        $this->logger->info("Getting profile for user: $userId");

        // In a real application, we would fetch the user from the database
        // For this example, we'll simulate a user
        $user = $this->userModel->getById($userId);

        if (!$user) {
            $this->logger->warning("User not found: $userId");
            return [
                'status' => 'error',
                'message' => 'User not found'
            ];
        }

        $this->logger->info("Profile retrieved for user: $userId");

        return [
            'status' => 'success',
            'data' => $user
        ];
    }

    /**
     * Update user profile
     * 
     * @param int $userId User ID
     * @param array $data Profile data to update
     * @return array Response with updated user profile data
     */
    public function updateProfile(int $userId, array $data): array
    {
        $this->logger->info("Updating profile for user: $userId");

        // In a real application, we would update the user in the database
        // For this example, we'll simulate an update
        $user = $this->userModel->getById($userId);

        if (!$user) {
            $this->logger->warning("User not found: $userId");
            return [
                'status' => 'error',
                'message' => 'User not found'
            ];
        }

        // Update the user data
        foreach ($data as $key => $value) {
            if (isset($user[$key])) {
                $user[$key] = $value;
            }
        }

        // Save the updated user
        $updatedUser = $this->userModel->update($userId, $user);

        $this->logger->info("Profile updated for user: $userId");

        // Send a notification email
        $this->emailSender->send(
            $updatedUser['email'],
            'Profile Updated',
            'Your profile has been updated successfully.'
        );

        return [
            'status' => 'success',
            'data' => $updatedUser
        ];
    }

    /**
     * Register a new user
     * 
     * @param array $data User registration data
     * @return array Response with registered user data
     */
    public function register(array $data): array
    {
        $this->logger->info("Registering new user with email: {$data['email']}");

        // Validate email format
        if (!$this->validator->isValidEmail($data['email'])) {
            $this->logger->warning("Invalid email format: {$data['email']}");
            return [
                'status' => 'error',
                'message' => 'Invalid email format'
            ];
        }

        // Validate password strength
        if (!$this->validator->isStrongPassword($data['password'])) {
            $this->logger->warning("Weak password for email: {$data['email']}");
            return [
                'status' => 'error',
                'message' => 'Password is too weak'
            ];
        }

        // In a real application, we would check if the email is already registered
        // For this example, we'll simulate a new user registration
        $userData = [
            'email' => $data['email'],
            'name' => $data['name'],
            'role' => 'user'
        ];

        // Create the user
        $user = $this->userModel->create($userData);

        $this->logger->info("User registered successfully: {$user['id']}");

        // Send a welcome email
        $this->emailSender->send(
            $user['email'],
            'Welcome to Our Platform',
            'Thank you for registering with us. Your account has been created successfully.'
        );

        return [
            'status' => 'success',
            'data' => $user
        ];
    }

    /**
     * Generate an authentication token
     * 
     * @param int $userId User ID
     * @return string Authentication token
     */
    private function generateToken(int $userId): string
    {
        // In a real application, we would generate a JWT or similar token
        // For this example, we'll just create a simple token
        return base64_encode("user_$userId" . time());
    }
}
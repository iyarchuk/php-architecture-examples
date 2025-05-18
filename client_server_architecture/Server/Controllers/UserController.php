<?php

namespace ClientServerArchitecture\Server\Controllers;

use ClientServerArchitecture\Server\Models\UserModel;
use ClientServerArchitecture\Shared\Request;
use ClientServerArchitecture\Shared\Response;

/**
 * UserController
 * 
 * This class handles user-related requests on the server side.
 * It processes requests, interacts with the UserModel, and generates responses.
 */
class UserController
{
    /**
     * @var UserModel The user model
     */
    private UserModel $userModel;

    /**
     * Constructor
     * 
     * @param UserModel $userModel
     */
    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    /**
     * Handle a request
     * 
     * @param Request $request
     * @return Response
     */
    public function handleRequest(Request $request): Response
    {
        $action = $request->getAction();
        $data = $request->getData();

        switch ($action) {
            case 'create':
                return $this->createUser($data);
            case 'get':
                return $this->getUser($data);
            case 'getAll':
                return $this->getAllUsers();
            case 'update':
                return $this->updateUser($data);
            case 'delete':
                return $this->deleteUser($data);
            case 'authenticate':
                return $this->authenticateUser($data);
            default:
                return new Response(false, null, "Unknown action: $action");
        }
    }

    /**
     * Create a user
     * 
     * @param array $data
     * @return Response
     */
    private function createUser(array $data): Response
    {
        try {
            // Validate required fields
            if (!isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
                return new Response(false, null, 'Missing required fields: name, email, password');
            }

            $user = $this->userModel->addUser($data['name'], $data['email'], $data['password']);
            
            // Remove password from response
            unset($user['password']);
            
            return new Response(true, $user, 'User created successfully');
        } catch (\InvalidArgumentException $e) {
            return new Response(false, null, $e->getMessage());
        } catch (\Exception $e) {
            return new Response(false, null, 'An error occurred while creating the user');
        }
    }

    /**
     * Get a user by ID
     * 
     * @param array $data
     * @return Response
     */
    private function getUser(array $data): Response
    {
        try {
            // Validate required fields
            if (!isset($data['id'])) {
                return new Response(false, null, 'Missing required field: id');
            }

            $user = $this->userModel->getUserById((int)$data['id']);
            
            if (!$user) {
                return new Response(false, null, 'User not found');
            }
            
            // Remove password from response
            unset($user['password']);
            
            return new Response(true, $user, 'User retrieved successfully');
        } catch (\Exception $e) {
            return new Response(false, null, 'An error occurred while retrieving the user');
        }
    }

    /**
     * Get all users
     * 
     * @return Response
     */
    private function getAllUsers(): Response
    {
        try {
            $users = $this->userModel->getAllUsers();
            
            // Remove passwords from response
            foreach ($users as &$user) {
                unset($user['password']);
            }
            
            return new Response(true, $users, 'Users retrieved successfully');
        } catch (\Exception $e) {
            return new Response(false, null, 'An error occurred while retrieving users');
        }
    }

    /**
     * Update a user
     * 
     * @param array $data
     * @return Response
     */
    private function updateUser(array $data): Response
    {
        try {
            // Validate required fields
            if (!isset($data['id'])) {
                return new Response(false, null, 'Missing required field: id');
            }

            $updateData = [];
            if (isset($data['name'])) $updateData['name'] = $data['name'];
            if (isset($data['email'])) $updateData['email'] = $data['email'];
            if (isset($data['password'])) $updateData['password'] = $data['password'];

            $user = $this->userModel->updateUser((int)$data['id'], $updateData);
            
            if (!$user) {
                return new Response(false, null, 'User not found');
            }
            
            // Remove password from response
            unset($user['password']);
            
            return new Response(true, $user, 'User updated successfully');
        } catch (\InvalidArgumentException $e) {
            return new Response(false, null, $e->getMessage());
        } catch (\Exception $e) {
            return new Response(false, null, 'An error occurred while updating the user');
        }
    }

    /**
     * Delete a user
     * 
     * @param array $data
     * @return Response
     */
    private function deleteUser(array $data): Response
    {
        try {
            // Validate required fields
            if (!isset($data['id'])) {
                return new Response(false, null, 'Missing required field: id');
            }

            $result = $this->userModel->deleteUser((int)$data['id']);
            
            if (!$result) {
                return new Response(false, null, 'User not found');
            }
            
            return new Response(true, null, 'User deleted successfully');
        } catch (\Exception $e) {
            return new Response(false, null, 'An error occurred while deleting the user');
        }
    }

    /**
     * Authenticate a user
     * 
     * @param array $data
     * @return Response
     */
    private function authenticateUser(array $data): Response
    {
        try {
            // Validate required fields
            if (!isset($data['email']) || !isset($data['password'])) {
                return new Response(false, null, 'Missing required fields: email, password');
            }

            $user = $this->userModel->authenticateUser($data['email'], $data['password']);
            
            if (!$user) {
                return new Response(false, null, 'Invalid email or password');
            }
            
            // Remove password from response
            unset($user['password']);
            
            return new Response(true, $user, 'Authentication successful');
        } catch (\Exception $e) {
            return new Response(false, null, 'An error occurred during authentication');
        }
    }
}
<?php

namespace MicroservicesArchitecture\Services\UserService;

use MicroservicesArchitecture\Services\BaseService;

/**
 * UserService
 * 
 * This class implements the user management microservice.
 * It provides endpoints for managing users, authentication, and profiles.
 */
class UserService extends BaseService
{
    /**
     * @var array The users database (in-memory for this example)
     */
    private array $users = [];
    
    /**
     * @var array The user profiles database (in-memory for this example)
     */
    private array $profiles = [];
    
    /**
     * @var array The user roles
     */
    private array $roles = [
        'user' => 'Regular User',
        'admin' => 'Administrator'
    ];
    
    /**
     * Constructor
     * 
     * @param string $url The URL of the service
     */
    public function __construct(string $url = 'http://user-service.example.com')
    {
        parent::__construct('user-service', $url);
        
        // Initialize with some sample data
        $this->initializeSampleData();
    }
    
    /**
     * Initialize the endpoints supported by the service
     * 
     * @return void
     */
    protected function initializeEndpoints(): void
    {
        $this->endpoints = [
            '/users' => [
                'methods' => ['GET', 'POST'],
                'handler' => [$this, 'handleUsers']
            ],
            '/users/{id}' => [
                'methods' => ['GET', 'PUT', 'DELETE'],
                'handler' => [$this, 'handleUser']
            ],
            '/users/{id}/profile' => [
                'methods' => ['GET', 'PUT'],
                'handler' => [$this, 'handleUserProfile']
            ],
            '/auth/login' => [
                'methods' => ['POST'],
                'handler' => [$this, 'handleLogin']
            ],
            '/auth/register' => [
                'methods' => ['POST'],
                'handler' => [$this, 'handleRegister']
            ],
            '/roles' => [
                'methods' => ['GET'],
                'handler' => [$this, 'handleRoles']
            ],
            '/health' => [
                'methods' => ['GET'],
                'handler' => [$this, 'handleHealth']
            ]
        ];
        
        // Define topics to subscribe to
        $this->subscribeTopics = [
            'order-created' => [$this, 'handleOrderCreatedEvent']
        ];
    }
    
    /**
     * Initialize sample data
     * 
     * @return void
     */
    private function initializeSampleData(): void
    {
        // Sample users
        $this->users = [
            1 => [
                'id' => 1,
                'email' => 'john.doe@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'user',
                'active' => true,
                'created_at' => date('Y-m-d H:i:s', strtotime('-30 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-30 days'))
            ],
            2 => [
                'id' => 2,
                'email' => 'jane.smith@example.com',
                'password' => password_hash('password456', PASSWORD_DEFAULT),
                'role' => 'user',
                'active' => true,
                'created_at' => date('Y-m-d H:i:s', strtotime('-15 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-15 days'))
            ],
            3 => [
                'id' => 3,
                'email' => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'active' => true,
                'created_at' => date('Y-m-d H:i:s', strtotime('-60 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-60 days'))
            ]
        ];
        
        // Sample profiles
        $this->profiles = [
            1 => [
                'user_id' => 1,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone' => '123-456-7890',
                'address' => '123 Main St, Anytown, USA',
                'bio' => 'Regular user with a passion for shopping',
                'updated_at' => date('Y-m-d H:i:s', strtotime('-30 days'))
            ],
            2 => [
                'user_id' => 2,
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'phone' => '987-654-3210',
                'address' => '456 Oak Ave, Somewhere, USA',
                'bio' => 'Frequent shopper and product reviewer',
                'updated_at' => date('Y-m-d H:i:s', strtotime('-15 days'))
            ],
            3 => [
                'user_id' => 3,
                'first_name' => 'Admin',
                'last_name' => 'User',
                'phone' => '555-555-5555',
                'address' => 'Admin Building, Server Room',
                'bio' => 'System administrator',
                'updated_at' => date('Y-m-d H:i:s', strtotime('-60 days'))
            ]
        ];
    }
    
    /**
     * Handle requests to the /users endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleUsers(string $method, array $data, array $headers): array
    {
        switch ($method) {
            case 'GET':
                // Filter out sensitive information
                $users = array_map(function ($user) {
                    unset($user['password']);
                    return $user;
                }, $this->users);
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => array_values($users)
                ];
            
            case 'POST':
                // This endpoint is for admin use only
                if (!$this->isAdmin($headers)) {
                    return [
                        'status' => 'error',
                        'code' => 403,
                        'message' => 'Forbidden'
                    ];
                }
                
                // Validate required fields
                if (empty($data['email']) || empty($data['password'])) {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Missing required fields'
                    ];
                }
                
                // Check if email is already in use
                foreach ($this->users as $user) {
                    if ($user['email'] === $data['email']) {
                        return [
                            'status' => 'error',
                            'code' => 400,
                            'message' => 'Email already in use'
                        ];
                    }
                }
                
                // Create new user
                $id = count($this->users) + 1;
                $user = [
                    'id' => $id,
                    'email' => $data['email'],
                    'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                    'role' => $data['role'] ?? 'user',
                    'active' => $data['active'] ?? true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                $this->users[$id] = $user;
                
                // Create profile
                $profile = [
                    'user_id' => $id,
                    'first_name' => $data['first_name'] ?? '',
                    'last_name' => $data['last_name'] ?? '',
                    'phone' => $data['phone'] ?? '',
                    'address' => $data['address'] ?? '',
                    'bio' => $data['bio'] ?? '',
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                $this->profiles[$id] = $profile;
                
                // Remove password from response
                unset($user['password']);
                
                return [
                    'status' => 'success',
                    'code' => 201,
                    'data' => [
                        'user' => $user,
                        'profile' => $profile
                    ]
                ];
            
            default:
                return [
                    'status' => 'error',
                    'code' => 405,
                    'message' => 'Method not allowed'
                ];
        }
    }
    
    /**
     * Handle requests to the /users/{id} endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleUser(string $method, array $data, array $headers): array
    {
        // Extract user ID from the endpoint
        $id = isset($data['id']) ? (int) $data['id'] : 0;
        
        // Check if user exists
        if (!isset($this->users[$id])) {
            return [
                'status' => 'error',
                'code' => 404,
                'message' => 'User not found'
            ];
        }
        
        // Check if the user is authorized to access this user
        if (!$this->isAdmin($headers) && !$this->isCurrentUser($headers, $id)) {
            return [
                'status' => 'error',
                'code' => 403,
                'message' => 'Forbidden'
            ];
        }
        
        switch ($method) {
            case 'GET':
                // Remove password from response
                $user = $this->users[$id];
                unset($user['password']);
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $user
                ];
            
            case 'PUT':
                // Update user
                if (isset($data['email'])) {
                    // Check if email is already in use by another user
                    foreach ($this->users as $userId => $user) {
                        if ($userId !== $id && $user['email'] === $data['email']) {
                            return [
                                'status' => 'error',
                                'code' => 400,
                                'message' => 'Email already in use'
                            ];
                        }
                    }
                    
                    $this->users[$id]['email'] = $data['email'];
                }
                
                if (isset($data['password'])) {
                    $this->users[$id]['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                }
                
                // Only admins can change roles and active status
                if ($this->isAdmin($headers)) {
                    if (isset($data['role'])) {
                        $this->users[$id]['role'] = $data['role'];
                    }
                    
                    if (isset($data['active'])) {
                        $this->users[$id]['active'] = (bool) $data['active'];
                    }
                }
                
                $this->users[$id]['updated_at'] = date('Y-m-d H:i:s');
                
                // Remove password from response
                $user = $this->users[$id];
                unset($user['password']);
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $user
                ];
            
            case 'DELETE':
                // Only admins can delete users
                if (!$this->isAdmin($headers)) {
                    return [
                        'status' => 'error',
                        'code' => 403,
                        'message' => 'Forbidden'
                    ];
                }
                
                // Delete user
                $user = $this->users[$id];
                unset($this->users[$id]);
                
                // Delete profile
                unset($this->profiles[$id]);
                
                // Remove password from response
                unset($user['password']);
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $user
                ];
            
            default:
                return [
                    'status' => 'error',
                    'code' => 405,
                    'message' => 'Method not allowed'
                ];
        }
    }
    
    /**
     * Handle requests to the /users/{id}/profile endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleUserProfile(string $method, array $data, array $headers): array
    {
        // Extract user ID from the endpoint
        $id = isset($data['id']) ? (int) $data['id'] : 0;
        
        // Check if user exists
        if (!isset($this->users[$id])) {
            return [
                'status' => 'error',
                'code' => 404,
                'message' => 'User not found'
            ];
        }
        
        // Check if profile exists
        if (!isset($this->profiles[$id])) {
            // Create empty profile
            $this->profiles[$id] = [
                'user_id' => $id,
                'first_name' => '',
                'last_name' => '',
                'phone' => '',
                'address' => '',
                'bio' => '',
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        
        // Check if the user is authorized to access this profile
        if (!$this->isAdmin($headers) && !$this->isCurrentUser($headers, $id)) {
            return [
                'status' => 'error',
                'code' => 403,
                'message' => 'Forbidden'
            ];
        }
        
        switch ($method) {
            case 'GET':
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $this->profiles[$id]
                ];
            
            case 'PUT':
                // Update profile
                if (isset($data['first_name'])) {
                    $this->profiles[$id]['first_name'] = $data['first_name'];
                }
                
                if (isset($data['last_name'])) {
                    $this->profiles[$id]['last_name'] = $data['last_name'];
                }
                
                if (isset($data['phone'])) {
                    $this->profiles[$id]['phone'] = $data['phone'];
                }
                
                if (isset($data['address'])) {
                    $this->profiles[$id]['address'] = $data['address'];
                }
                
                if (isset($data['bio'])) {
                    $this->profiles[$id]['bio'] = $data['bio'];
                }
                
                $this->profiles[$id]['updated_at'] = date('Y-m-d H:i:s');
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $this->profiles[$id]
                ];
            
            default:
                return [
                    'status' => 'error',
                    'code' => 405,
                    'message' => 'Method not allowed'
                ];
        }
    }
    
    /**
     * Handle requests to the /auth/login endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleLogin(string $method, array $data, array $headers): array
    {
        switch ($method) {
            case 'POST':
                // Validate required fields
                if (empty($data['email']) || empty($data['password'])) {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Missing required fields'
                    ];
                }
                
                // Find user by email
                $user = null;
                foreach ($this->users as $u) {
                    if ($u['email'] === $data['email']) {
                        $user = $u;
                        break;
                    }
                }
                
                // Check if user exists
                if (!$user) {
                    return [
                        'status' => 'error',
                        'code' => 401,
                        'message' => 'Invalid credentials'
                    ];
                }
                
                // Check if user is active
                if (!$user['active']) {
                    return [
                        'status' => 'error',
                        'code' => 401,
                        'message' => 'Account is inactive'
                    ];
                }
                
                // Verify password
                if (!password_verify($data['password'], $user['password'])) {
                    return [
                        'status' => 'error',
                        'code' => 401,
                        'message' => 'Invalid credentials'
                    ];
                }
                
                // Generate token (in a real implementation, this would be a JWT)
                $token = bin2hex(random_bytes(32));
                
                // Remove password from response
                unset($user['password']);
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => [
                        'user' => $user,
                        'token' => $token
                    ]
                ];
            
            default:
                return [
                    'status' => 'error',
                    'code' => 405,
                    'message' => 'Method not allowed'
                ];
        }
    }
    
    /**
     * Handle requests to the /auth/register endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleRegister(string $method, array $data, array $headers): array
    {
        switch ($method) {
            case 'POST':
                // Validate required fields
                if (empty($data['email']) || empty($data['password'])) {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Missing required fields'
                    ];
                }
                
                // Check if email is already in use
                foreach ($this->users as $user) {
                    if ($user['email'] === $data['email']) {
                        return [
                            'status' => 'error',
                            'code' => 400,
                            'message' => 'Email already in use'
                        ];
                    }
                }
                
                // Create new user
                $id = count($this->users) + 1;
                $user = [
                    'id' => $id,
                    'email' => $data['email'],
                    'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                    'role' => 'user', // New registrations are always regular users
                    'active' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                $this->users[$id] = $user;
                
                // Create profile
                $profile = [
                    'user_id' => $id,
                    'first_name' => $data['first_name'] ?? '',
                    'last_name' => $data['last_name'] ?? '',
                    'phone' => $data['phone'] ?? '',
                    'address' => $data['address'] ?? '',
                    'bio' => $data['bio'] ?? '',
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                $this->profiles[$id] = $profile;
                
                // Generate token (in a real implementation, this would be a JWT)
                $token = bin2hex(random_bytes(32));
                
                // Remove password from response
                unset($user['password']);
                
                return [
                    'status' => 'success',
                    'code' => 201,
                    'data' => [
                        'user' => $user,
                        'profile' => $profile,
                        'token' => $token
                    ]
                ];
            
            default:
                return [
                    'status' => 'error',
                    'code' => 405,
                    'message' => 'Method not allowed'
                ];
        }
    }
    
    /**
     * Handle requests to the /roles endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleRoles(string $method, array $data, array $headers): array
    {
        switch ($method) {
            case 'GET':
                $roles = [];
                
                foreach ($this->roles as $key => $value) {
                    $roles[] = [
                        'code' => $key,
                        'name' => $value
                    ];
                }
                
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $roles
                ];
            
            default:
                return [
                    'status' => 'error',
                    'code' => 405,
                    'message' => 'Method not allowed'
                ];
        }
    }
    
    /**
     * Handle requests to the /health endpoint
     * 
     * @param string $method The HTTP method
     * @param array $data The request data
     * @param array $headers The request headers
     * @return array The response
     */
    public function handleHealth(string $method, array $data, array $headers): array
    {
        return [
            'status' => 'success',
            'code' => 200,
            'data' => [
                'service' => $this->name,
                'status' => 'healthy',
                'timestamp' => date('Y-m-d H:i:s'),
                'version' => '1.0.0'
            ]
        ];
    }
    
    /**
     * Handle the order-created event
     * 
     * @param array $event The event data
     * @return void
     */
    public function handleOrderCreatedEvent(array $event): void
    {
        // In a real implementation, this might update user statistics, send notifications, etc.
        // For this example, we'll just log the event
        error_log("Order created for user {$event['message']['user_id']} with total {$event['message']['total']}");
    }
    
    /**
     * Check if the current user is an admin
     * 
     * @param array $headers The request headers
     * @return bool True if the user is an admin, false otherwise
     */
    private function isAdmin(array $headers): bool
    {
        // In a real implementation, this would verify the JWT token in the Authorization header
        // For this example, we'll just check if the X-User-Role header is set to 'admin'
        return isset($headers['X-User-Role']) && $headers['X-User-Role'] === 'admin';
    }
    
    /**
     * Check if the current user is the specified user
     * 
     * @param array $headers The request headers
     * @param int $userId The user ID to check
     * @return bool True if the current user is the specified user, false otherwise
     */
    private function isCurrentUser(array $headers, int $userId): bool
    {
        // In a real implementation, this would verify the JWT token in the Authorization header
        // For this example, we'll just check if the X-User-ID header matches the specified user ID
        return isset($headers['X-User-ID']) && (int) $headers['X-User-ID'] === $userId;
    }
}
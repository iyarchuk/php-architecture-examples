/**
 * ContainerApp class
 * 
 * The main container application that manages the microfrontends
 */
class ContainerApp {
    /**
     * Constructor
     * 
     * @param {Router} router - The router instance
     */
    constructor(router) {
        this.router = router;
        this.container = document.getElementById('microfrontend-container');
        this.currentMicrofrontend = null;
        this.user = {
            isLoggedIn: false,
            username: 'Guest',
            id: null
        };
        
        // Event bus for communication between microfrontends
        this.eventBus = window.EventBus || {
            publish: (event, data) => {
                console.log(`Event published: ${event}`, data);
                const customEvent = new CustomEvent(event, { detail: data });
                document.dispatchEvent(customEvent);
            },
            subscribe: (event, callback) => {
                document.addEventListener(event, (e) => callback(e.detail));
                console.log(`Subscribed to event: ${event}`);
            }
        };
        
        // Make event bus globally available
        window.EventBus = this.eventBus;
    }
    
    /**
     * Initialize the container application
     */
    init() {
        console.log('Initializing container application...');
        
        // Set up event listeners
        this.setupEventListeners();
        
        // Set up router
        this.router.init();
        
        // Load the default microfrontend
        this.router.navigateTo('product-catalog');
        
        console.log('Container application initialized');
    }
    
    /**
     * Set up event listeners
     */
    setupEventListeners() {
        // Login button
        const loginButton = document.getElementById('login-button');
        loginButton.addEventListener('click', () => this.handleLogin());
        
        // Subscribe to cart update events
        this.eventBus.subscribe('cart:updated', (data) => {
            console.log('Cart updated:', data);
            // Update cart badge or other UI elements
        });
        
        // Subscribe to user login events
        this.eventBus.subscribe('user:login', (data) => {
            this.updateUserInfo(data);
        });
        
        // Subscribe to user logout events
        this.eventBus.subscribe('user:logout', () => {
            this.user = {
                isLoggedIn: false,
                username: 'Guest',
                id: null
            };
            this.updateUserUI();
        });
    }
    
    /**
     * Handle login button click
     */
    handleLogin() {
        if (this.user.isLoggedIn) {
            // Logout
            this.eventBus.publish('user:logout', {});
        } else {
            // In a real application, this would show a login form
            // For this example, we'll just simulate a successful login
            const userData = {
                isLoggedIn: true,
                username: 'JohnDoe',
                id: 123
            };
            this.eventBus.publish('user:login', userData);
        }
    }
    
    /**
     * Update user information
     * 
     * @param {Object} userData - User data
     */
    updateUserInfo(userData) {
        this.user = {
            isLoggedIn: userData.isLoggedIn,
            username: userData.username,
            id: userData.id
        };
        this.updateUserUI();
    }
    
    /**
     * Update user UI elements
     */
    updateUserUI() {
        const usernameElement = document.getElementById('username');
        const loginButton = document.getElementById('login-button');
        
        usernameElement.textContent = this.user.username;
        loginButton.textContent = this.user.isLoggedIn ? 'Logout' : 'Login';
    }
    
    /**
     * Load a microfrontend
     * 
     * @param {string} name - The name of the microfrontend to load
     */
    loadMicrofrontend(name) {
        console.log(`Loading microfrontend: ${name}`);
        
        // Clear the container
        this.container.innerHTML = '<p>Loading...</p>';
        
        // In a real application, this would dynamically load the microfrontend's JavaScript
        // For this example, we'll simulate loading different microfrontends
        setTimeout(() => {
            let content = '';
            
            switch (name) {
                case 'product-catalog':
                    content = `
                        <h2>Product Catalog</h2>
                        <div class="product-list">
                            <div class="product">
                                <h3>Product 1</h3>
                                <p>Description of product 1</p>
                                <p>Price: $19.99</p>
                                <button class="add-to-cart" data-product-id="1">Add to Cart</button>
                            </div>
                            <div class="product">
                                <h3>Product 2</h3>
                                <p>Description of product 2</p>
                                <p>Price: $29.99</p>
                                <button class="add-to-cart" data-product-id="2">Add to Cart</button>
                            </div>
                        </div>
                    `;
                    break;
                case 'shopping-cart':
                    content = `
                        <h2>Shopping Cart</h2>
                        <div class="cart-items">
                            <p>Your cart is empty</p>
                        </div>
                        <button id="checkout-button" disabled>Checkout</button>
                    `;
                    break;
                case 'user-profile':
                    content = `
                        <h2>User Profile</h2>
                        ${this.user.isLoggedIn 
                            ? `
                                <div class="user-details">
                                    <p><strong>Username:</strong> ${this.user.username}</p>
                                    <p><strong>User ID:</strong> ${this.user.id}</p>
                                </div>
                                <h3>Order History</h3>
                                <p>No orders yet</p>
                            `
                            : `
                                <p>Please log in to view your profile</p>
                                <button id="profile-login-button">Login</button>
                            `
                        }
                    `;
                    break;
                default:
                    content = `<p>Microfrontend "${name}" not found</p>`;
            }
            
            this.container.innerHTML = content;
            
            // Set up event listeners for the loaded microfrontend
            this.setupMicrofrontendEventListeners(name);
            
            console.log(`Microfrontend "${name}" loaded`);
        }, 500); // Simulate loading delay
    }
    
    /**
     * Set up event listeners for the loaded microfrontend
     * 
     * @param {string} name - The name of the loaded microfrontend
     */
    setupMicrofrontendEventListeners(name) {
        switch (name) {
            case 'product-catalog':
                // Add to cart buttons
                const addToCartButtons = document.querySelectorAll('.add-to-cart');
                addToCartButtons.forEach(button => {
                    button.addEventListener('click', (e) => {
                        const productId = e.target.getAttribute('data-product-id');
                        const productName = e.target.parentElement.querySelector('h3').textContent;
                        const productPrice = e.target.parentElement.querySelector('p:nth-of-type(2)').textContent.split('$')[1];
                        
                        // Publish cart:add event
                        this.eventBus.publish('cart:add', {
                            id: productId,
                            name: productName,
                            price: parseFloat(productPrice),
                            quantity: 1
                        });
                        
                        alert(`Added ${productName} to cart`);
                    });
                });
                break;
            case 'user-profile':
                if (!this.user.isLoggedIn) {
                    const loginButton = document.getElementById('profile-login-button');
                    if (loginButton) {
                        loginButton.addEventListener('click', () => this.handleLogin());
                    }
                }
                break;
        }
    }
}
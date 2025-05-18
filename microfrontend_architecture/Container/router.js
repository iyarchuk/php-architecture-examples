/**
 * Router class
 * 
 * Handles routing between different microfrontends
 */
class Router {
    /**
     * Constructor
     */
    constructor() {
        this.routes = {
            'product-catalog': {
                title: 'Product Catalog',
                load: (app) => app.loadMicrofrontend('product-catalog')
            },
            'shopping-cart': {
                title: 'Shopping Cart',
                load: (app) => app.loadMicrofrontend('shopping-cart')
            },
            'user-profile': {
                title: 'User Profile',
                load: (app) => app.loadMicrofrontend('user-profile')
            }
        };
        this.defaultRoute = 'product-catalog';
        this.currentRoute = null;
    }
    
    /**
     * Initialize the router
     */
    init() {
        console.log('Initializing router...');
        
        // Set up event listeners for navigation links
        const navLinks = document.querySelectorAll('nav a[data-route]');
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const route = e.target.getAttribute('data-route');
                this.navigateTo(route);
            });
        });
        
        // Handle browser back/forward buttons
        window.addEventListener('popstate', (e) => {
            const route = e.state ? e.state.route : this.defaultRoute;
            this.loadRoute(route, false);
        });
        
        console.log('Router initialized');
    }
    
    /**
     * Navigate to a specific route
     * 
     * @param {string} route - The route to navigate to
     */
    navigateTo(route) {
        console.log(`Navigating to route: ${route}`);
        
        if (!this.routes[route]) {
            console.error(`Route not found: ${route}`);
            route = this.defaultRoute;
        }
        
        // Update browser history
        window.history.pushState({ route }, this.routes[route].title, `#${route}`);
        
        // Load the route
        this.loadRoute(route);
    }
    
    /**
     * Load a specific route
     * 
     * @param {string} route - The route to load
     * @param {boolean} updateActive - Whether to update the active navigation link
     */
    loadRoute(route, updateActive = true) {
        console.log(`Loading route: ${route}`);
        
        if (!this.routes[route]) {
            console.error(`Route not found: ${route}`);
            return;
        }
        
        // Update current route
        this.currentRoute = route;
        
        // Update document title
        document.title = `Microfrontend Example - ${this.routes[route].title}`;
        
        // Update active navigation link
        if (updateActive) {
            const navLinks = document.querySelectorAll('nav a[data-route]');
            navLinks.forEach(link => {
                if (link.getAttribute('data-route') === route) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        }
        
        // Load the microfrontend
        const app = window.app || document.querySelector('script').app;
        this.routes[route].load(app);
    }
}
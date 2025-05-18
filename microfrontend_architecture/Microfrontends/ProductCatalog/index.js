/**
 * ProductCatalog Microfrontend
 * 
 * Entry point for the Product Catalog microfrontend
 */

// ProductCatalog Microfrontend
(function() {
    // Define the ProductCatalog class
    function ProductCatalog(eventBus, stateManager) {
        this.eventBus = eventBus;
        this.stateManager = stateManager;
        this.products = [
            {
                id: 1,
                name: 'Smartphone X',
                description: 'Latest smartphone with amazing features',
                price: 999,
                image: 'smartphone.jpg'
            },
            {
                id: 2,
                name: 'Laptop Pro',
                description: 'Powerful laptop for professionals',
                price: 1499,
                image: 'laptop.jpg'
            },
            {
                id: 3,
                name: 'Wireless Headphones',
                description: 'Premium sound quality with noise cancellation',
                price: 299,
                image: 'headphones.jpg'
            }
        ];
        this.eventHandlers = {};
    }

    // Render the product catalog
    ProductCatalog.prototype.render = function(container) {
        // Create the product catalog container
        var catalogContainer = document.createElement('div');
        catalogContainer.className = 'product-catalog';

        // Create the header
        var header = document.createElement('h2');
        header.textContent = 'Product Catalog';
        catalogContainer.appendChild(header);

        // Create the product list
        var productList = document.createElement('div');
        productList.className = 'product-list';

        // Add products to the list
        for (var i = 0; i < this.products.length; i++) {
            var product = this.products[i];
            var productElement = this.createProductElement(product);
            productList.appendChild(productElement);
        }

        catalogContainer.appendChild(productList);

        // Clear the container and append the catalog
        container.innerHTML = '';
        container.appendChild(catalogContainer);

        // Subscribe to events
        this.subscribeToEvents();
    };

    // Create a product element
    ProductCatalog.prototype.createProductElement = function(product) {
        var self = this;
        var productElement = document.createElement('div');
        productElement.className = 'product';
        productElement.dataset.productId = product.id;

        var name = document.createElement('h3');
        name.textContent = product.name;

        var description = document.createElement('p');
        description.textContent = product.description;

        var price = document.createElement('p');
        price.className = 'price';
        price.textContent = '$' + product.price;

        var addToCartButton = document.createElement('button');
        addToCartButton.className = 'add-to-cart-button';
        addToCartButton.textContent = 'Add to Cart';
        addToCartButton.addEventListener('click', function() {
            self.addToCart(product);
        });

        productElement.appendChild(name);
        productElement.appendChild(description);
        productElement.appendChild(price);
        productElement.appendChild(addToCartButton);

        return productElement;
    };

    // Add a product to the cart
    ProductCatalog.prototype.addToCart = function(product) {
        // Publish an event to add the product to the cart
        this.eventBus.publish('cart:add', {
            id: product.id,
            name: product.name,
            price: product.price,
            quantity: 1
        });
    };

    // Subscribe to events
    ProductCatalog.prototype.subscribeToEvents = function() {
        var self = this;

        // Subscribe to product search events
        this.eventHandlers['product:search'] = this.eventBus.subscribe('product:search', function(data) {
            self.searchProducts(data.query);
        });
    };

    // Search products
    ProductCatalog.prototype.searchProducts = function(query) {
        // Filter products based on the query
        var filteredProducts = this.products.filter(function(product) {
            return product.name.toLowerCase().includes(query.toLowerCase()) ||
                   product.description.toLowerCase().includes(query.toLowerCase());
        });

        // Update the UI with filtered products
        var productList = document.querySelector('.product-list');
        if (productList) {
            productList.innerHTML = '';

            for (var i = 0; i < filteredProducts.length; i++) {
                var product = filteredProducts[i];
                var productElement = this.createProductElement(product);
                productList.appendChild(productElement);
            }
        }
    };

    // Clean up resources
    ProductCatalog.prototype.destroy = function() {
        // Unsubscribe from events
        for (var event in this.eventHandlers) {
            if (this.eventHandlers.hasOwnProperty(event) && typeof this.eventHandlers[event] === 'function') {
                this.eventHandlers[event]();
            }
        }

        // Clear event handlers
        this.eventHandlers = {};
    };

    // Define the mount function that will be called by the container
    function mount(containerId, eventBus, stateManager) {
        // Get the container element
        var container = document.getElementById(containerId);

        if (!container) {
            console.error('Container element with id "' + containerId + '" not found');
            return;
        }

        // Create an instance of the ProductCatalog component
        var productCatalog = new ProductCatalog(eventBus, stateManager);

        // Render the component in the container
        productCatalog.render(container);

        // Return an unmount function
        return function unmount() {
            // Clean up any event listeners or resources
            productCatalog.destroy();
            // Clear the container
            container.innerHTML = '';
        };
    }

    // Expose the mount function
    window.ProductCatalogMicrofrontend = {
        mount: mount
    };

    // If the microfrontend is loaded directly (not via the container)
    if (typeof window !== 'undefined' && !window.isLoaded) {
        // Create a standalone version for development/testing
        window.addEventListener('DOMContentLoaded', function() {
            // Create mock event bus and state manager if not provided
            var mockEventBus = window.EventBus || {
                publish: function(event, data) {
                    console.log('Event published: ' + event, data);
                },
                subscribe: function(event, callback) {
                    console.log('Subscribed to event: ' + event);
                    return function() {};
                }
            };

            var mockStateManager = window.StateManager || {
                getState: function(key) {
                    return {};
                },
                setState: function(key, value) {
                    console.log('State updated: ' + key, value);
                }
            };

            // Mount the microfrontend in a default container
            var defaultContainer = document.createElement('div');
            defaultContainer.id = 'product-catalog-container';
            document.body.appendChild(defaultContainer);

            mount('product-catalog-container', mockEventBus, mockStateManager);
        });
    }
})();

/**
 * EventBus class
 * 
 * Provides a communication mechanism between microfrontends using the publish-subscribe pattern
 */
class EventBus {
    /**
     * Constructor
     */
    constructor() {
        this.events = {};
        this.debug = false;
    }
    
    /**
     * Enable or disable debug mode
     * 
     * @param {boolean} enabled - Whether debug mode should be enabled
     */
    setDebug(enabled) {
        this.debug = enabled;
    }
    
    /**
     * Subscribe to an event
     * 
     * @param {string} event - The event name
     * @param {Function} callback - The callback function to execute when the event is published
     * @returns {Function} - Unsubscribe function
     */
    subscribe(event, callback) {
        if (!this.events[event]) {
            this.events[event] = [];
        }
        
        this.events[event].push(callback);
        
        if (this.debug) {
            console.log(`[EventBus] Subscribed to event: ${event}`);
        }
        
        // Return unsubscribe function
        return () => {
            this.events[event] = this.events[event].filter(cb => cb !== callback);
            
            if (this.debug) {
                console.log(`[EventBus] Unsubscribed from event: ${event}`);
            }
        };
    }
    
    /**
     * Publish an event
     * 
     * @param {string} event - The event name
     * @param {*} data - The data to pass to subscribers
     */
    publish(event, data) {
        if (!this.events[event]) {
            if (this.debug) {
                console.log(`[EventBus] No subscribers for event: ${event}`);
            }
            return;
        }
        
        if (this.debug) {
            console.log(`[EventBus] Publishing event: ${event}`, data);
        }
        
        this.events[event].forEach(callback => {
            try {
                callback(data);
            } catch (error) {
                console.error(`[EventBus] Error in subscriber callback for event ${event}:`, error);
            }
        });
    }
    
    /**
     * Clear all subscribers for an event
     * 
     * @param {string} event - The event name
     */
    clear(event) {
        if (event) {
            delete this.events[event];
            
            if (this.debug) {
                console.log(`[EventBus] Cleared all subscribers for event: ${event}`);
            }
        } else {
            this.events = {};
            
            if (this.debug) {
                console.log(`[EventBus] Cleared all subscribers for all events`);
            }
        }
    }
    
    /**
     * Get the number of subscribers for an event
     * 
     * @param {string} event - The event name
     * @returns {number} - The number of subscribers
     */
    getSubscriberCount(event) {
        if (!this.events[event]) {
            return 0;
        }
        
        return this.events[event].length;
    }
    
    /**
     * Check if an event has subscribers
     * 
     * @param {string} event - The event name
     * @returns {boolean} - Whether the event has subscribers
     */
    hasSubscribers(event) {
        return this.getSubscriberCount(event) > 0;
    }
}

// Create a singleton instance
const eventBus = new EventBus();

// Export the singleton instance
if (typeof module !== 'undefined' && module.exports) {
    module.exports = eventBus;
} else {
    window.EventBus = eventBus;
}
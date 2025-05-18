/**
 * StateManager class
 * 
 * Provides a shared state management solution for microfrontends
 */
class StateManager {
    /**
     * Constructor
     * 
     * @param {EventBus} eventBus - The event bus instance
     */
    constructor(eventBus) {
        this.state = {};
        this.eventBus = eventBus || (typeof window !== 'undefined' ? window.EventBus : null);
        this.debug = false;
        
        if (!this.eventBus) {
            console.error('[StateManager] EventBus not provided and not found in window');
        }
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
     * Get the current state or a specific part of it
     * 
     * @param {string} [key] - The key to get (optional)
     * @returns {*} - The state or part of it
     */
    getState(key) {
        if (key) {
            return this.state[key];
        }
        
        return { ...this.state };
    }
    
    /**
     * Set a value in the state
     * 
     * @param {string} key - The key to set
     * @param {*} value - The value to set
     * @param {boolean} [silent=false] - Whether to skip publishing the state change event
     */
    setState(key, value, silent = false) {
        const oldValue = this.state[key];
        this.state[key] = value;
        
        if (this.debug) {
            console.log(`[StateManager] State updated: ${key}`, {
                oldValue,
                newValue: value
            });
        }
        
        if (!silent && this.eventBus) {
            this.eventBus.publish('state:changed', {
                key,
                oldValue,
                newValue: value
            });
            
            // Also publish a specific event for this key
            this.eventBus.publish(`state:changed:${key}`, {
                oldValue,
                newValue: value
            });
        }
    }
    
    /**
     * Update multiple values in the state
     * 
     * @param {Object} updates - The updates to apply
     * @param {boolean} [silent=false] - Whether to skip publishing the state change event
     */
    updateState(updates, silent = false) {
        const changes = {};
        
        Object.keys(updates).forEach(key => {
            const oldValue = this.state[key];
            const newValue = updates[key];
            
            if (oldValue !== newValue) {
                this.state[key] = newValue;
                changes[key] = {
                    oldValue,
                    newValue
                };
            }
        });
        
        if (this.debug) {
            console.log('[StateManager] State updated with multiple values', changes);
        }
        
        if (!silent && this.eventBus && Object.keys(changes).length > 0) {
            this.eventBus.publish('state:changed:batch', changes);
            
            // Also publish individual events
            Object.keys(changes).forEach(key => {
                this.eventBus.publish(`state:changed:${key}`, {
                    oldValue: changes[key].oldValue,
                    newValue: changes[key].newValue
                });
            });
        }
    }
    
    /**
     * Remove a key from the state
     * 
     * @param {string} key - The key to remove
     * @param {boolean} [silent=false] - Whether to skip publishing the state change event
     */
    removeState(key, silent = false) {
        if (!(key in this.state)) {
            return;
        }
        
        const oldValue = this.state[key];
        delete this.state[key];
        
        if (this.debug) {
            console.log(`[StateManager] State removed: ${key}`, {
                oldValue
            });
        }
        
        if (!silent && this.eventBus) {
            this.eventBus.publish('state:removed', {
                key,
                oldValue
            });
            
            // Also publish a specific event for this key
            this.eventBus.publish(`state:removed:${key}`, {
                oldValue
            });
        }
    }
    
    /**
     * Clear the entire state
     * 
     * @param {boolean} [silent=false] - Whether to skip publishing the state change event
     */
    clearState(silent = false) {
        const oldState = { ...this.state };
        this.state = {};
        
        if (this.debug) {
            console.log('[StateManager] State cleared', {
                oldState
            });
        }
        
        if (!silent && this.eventBus) {
            this.eventBus.publish('state:cleared', {
                oldState
            });
        }
    }
    
    /**
     * Subscribe to state changes
     * 
     * @param {string} key - The key to subscribe to
     * @param {Function} callback - The callback function
     * @returns {Function} - Unsubscribe function
     */
    subscribe(key, callback) {
        if (!this.eventBus) {
            console.error('[StateManager] Cannot subscribe: EventBus not available');
            return () => {};
        }
        
        return this.eventBus.subscribe(`state:changed:${key}`, callback);
    }
    
    /**
     * Subscribe to all state changes
     * 
     * @param {Function} callback - The callback function
     * @returns {Function} - Unsubscribe function
     */
    subscribeToAll(callback) {
        if (!this.eventBus) {
            console.error('[StateManager] Cannot subscribe: EventBus not available');
            return () => {};
        }
        
        return this.eventBus.subscribe('state:changed', callback);
    }
}

// Create a singleton instance
const stateManager = new StateManager(
    typeof window !== 'undefined' ? window.EventBus : null
);

// Export the singleton instance
if (typeof module !== 'undefined' && module.exports) {
    module.exports = stateManager;
} else {
    window.StateManager = stateManager;
}
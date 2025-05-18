/**
 * Button component
 * 
 * A reusable button component that can be used across microfrontends
 */
class Button {
    /**
     * Constructor
     * 
     * @param {Object} options - Button options
     * @param {string} options.text - Button text
     * @param {string} [options.type='primary'] - Button type (primary, secondary, danger)
     * @param {string} [options.size='medium'] - Button size (small, medium, large)
     * @param {boolean} [options.disabled=false] - Whether the button is disabled
     * @param {Function} [options.onClick] - Click handler
     */
    constructor(options) {
        this.options = {
            text: options.text || 'Button',
            type: options.type || 'primary',
            size: options.size || 'medium',
            disabled: options.disabled || false,
            onClick: options.onClick || (() => {})
        };
        
        this.element = null;
    }
    
    /**
     * Render the button
     * 
     * @param {HTMLElement} container - The container to render the button in
     * @returns {HTMLElement} - The rendered button
     */
    render(container) {
        // Create button element
        this.element = document.createElement('button');
        this.element.textContent = this.options.text;
        this.element.className = `btn btn-${this.options.type} btn-${this.options.size}`;
        
        // Add styles
        this.element.style.display = 'inline-block';
        this.element.style.padding = this.getPadding();
        this.element.style.borderRadius = '4px';
        this.element.style.border = '1px solid transparent';
        this.element.style.cursor = this.options.disabled ? 'not-allowed' : 'pointer';
        this.element.style.fontSize = this.getFontSize();
        this.element.style.fontWeight = 'bold';
        this.element.style.textAlign = 'center';
        this.element.style.transition = 'background-color 0.2s, border-color 0.2s, color 0.2s';
        
        // Set colors based on type
        this.setColors();
        
        // Set disabled state
        if (this.options.disabled) {
            this.element.disabled = true;
            this.element.style.opacity = '0.65';
        }
        
        // Add click handler
        this.element.addEventListener('click', (e) => {
            if (!this.options.disabled) {
                this.options.onClick(e);
            }
        });
        
        // Add hover effect
        this.element.addEventListener('mouseenter', () => {
            if (!this.options.disabled) {
                this.element.style.filter = 'brightness(90%)';
            }
        });
        
        this.element.addEventListener('mouseleave', () => {
            if (!this.options.disabled) {
                this.element.style.filter = 'brightness(100%)';
            }
        });
        
        // Append to container if provided
        if (container) {
            container.appendChild(this.element);
        }
        
        return this.element;
    }
    
    /**
     * Set button colors based on type
     */
    setColors() {
        switch (this.options.type) {
            case 'primary':
                this.element.style.backgroundColor = '#007bff';
                this.element.style.borderColor = '#007bff';
                this.element.style.color = '#fff';
                break;
            case 'secondary':
                this.element.style.backgroundColor = '#6c757d';
                this.element.style.borderColor = '#6c757d';
                this.element.style.color = '#fff';
                break;
            case 'success':
                this.element.style.backgroundColor = '#28a745';
                this.element.style.borderColor = '#28a745';
                this.element.style.color = '#fff';
                break;
            case 'danger':
                this.element.style.backgroundColor = '#dc3545';
                this.element.style.borderColor = '#dc3545';
                this.element.style.color = '#fff';
                break;
            case 'warning':
                this.element.style.backgroundColor = '#ffc107';
                this.element.style.borderColor = '#ffc107';
                this.element.style.color = '#212529';
                break;
            case 'info':
                this.element.style.backgroundColor = '#17a2b8';
                this.element.style.borderColor = '#17a2b8';
                this.element.style.color = '#fff';
                break;
            case 'light':
                this.element.style.backgroundColor = '#f8f9fa';
                this.element.style.borderColor = '#f8f9fa';
                this.element.style.color = '#212529';
                break;
            case 'dark':
                this.element.style.backgroundColor = '#343a40';
                this.element.style.borderColor = '#343a40';
                this.element.style.color = '#fff';
                break;
            case 'outline-primary':
                this.element.style.backgroundColor = 'transparent';
                this.element.style.borderColor = '#007bff';
                this.element.style.color = '#007bff';
                break;
            default:
                this.element.style.backgroundColor = '#007bff';
                this.element.style.borderColor = '#007bff';
                this.element.style.color = '#fff';
        }
    }
    
    /**
     * Get padding based on size
     * 
     * @returns {string} - CSS padding value
     */
    getPadding() {
        switch (this.options.size) {
            case 'small':
                return '0.25rem 0.5rem';
            case 'large':
                return '0.5rem 1rem';
            case 'medium':
            default:
                return '0.375rem 0.75rem';
        }
    }
    
    /**
     * Get font size based on size
     * 
     * @returns {string} - CSS font-size value
     */
    getFontSize() {
        switch (this.options.size) {
            case 'small':
                return '0.875rem';
            case 'large':
                return '1.25rem';
            case 'medium':
            default:
                return '1rem';
        }
    }
    
    /**
     * Update button text
     * 
     * @param {string} text - New button text
     */
    setText(text) {
        this.options.text = text;
        if (this.element) {
            this.element.textContent = text;
        }
    }
    
    /**
     * Enable or disable the button
     * 
     * @param {boolean} disabled - Whether the button should be disabled
     */
    setDisabled(disabled) {
        this.options.disabled = disabled;
        if (this.element) {
            this.element.disabled = disabled;
            this.element.style.opacity = disabled ? '0.65' : '1';
            this.element.style.cursor = disabled ? 'not-allowed' : 'pointer';
        }
    }
    
    /**
     * Set button type
     * 
     * @param {string} type - Button type
     */
    setType(type) {
        this.options.type = type;
        if (this.element) {
            this.element.className = `btn btn-${type} btn-${this.options.size}`;
            this.setColors();
        }
    }
}

// Export the Button class
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Button;
} else {
    window.Button = Button;
}
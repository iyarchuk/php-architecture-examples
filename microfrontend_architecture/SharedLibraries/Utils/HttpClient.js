/**
 * HttpClient class
 * 
 * A simple HTTP client for making API requests from microfrontends
 */
class HttpClient {
    /**
     * Constructor
     * 
     * @param {Object} [options] - Configuration options
     * @param {string} [options.baseUrl=''] - Base URL for all requests
     * @param {Object} [options.headers={}] - Default headers for all requests
     * @param {number} [options.timeout=30000] - Default timeout in milliseconds
     * @param {boolean} [options.withCredentials=false] - Whether to include credentials in cross-origin requests
     */
    constructor(options = {}) {
        this.baseUrl = options.baseUrl || '';
        this.defaultHeaders = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...options.headers
        };
        this.timeout = options.timeout || 30000;
        this.withCredentials = options.withCredentials || false;
    }
    
    /**
     * Make a GET request
     * 
     * @param {string} url - The URL to request
     * @param {Object} [options] - Request options
     * @param {Object} [options.headers] - Additional headers
     * @param {number} [options.timeout] - Request timeout
     * @param {boolean} [options.withCredentials] - Whether to include credentials
     * @returns {Promise<Object>} - Response data
     */
    async get(url, options = {}) {
        return this.request('GET', url, null, options);
    }
    
    /**
     * Make a POST request
     * 
     * @param {string} url - The URL to request
     * @param {Object} data - The data to send
     * @param {Object} [options] - Request options
     * @param {Object} [options.headers] - Additional headers
     * @param {number} [options.timeout] - Request timeout
     * @param {boolean} [options.withCredentials] - Whether to include credentials
     * @returns {Promise<Object>} - Response data
     */
    async post(url, data, options = {}) {
        return this.request('POST', url, data, options);
    }
    
    /**
     * Make a PUT request
     * 
     * @param {string} url - The URL to request
     * @param {Object} data - The data to send
     * @param {Object} [options] - Request options
     * @param {Object} [options.headers] - Additional headers
     * @param {number} [options.timeout] - Request timeout
     * @param {boolean} [options.withCredentials] - Whether to include credentials
     * @returns {Promise<Object>} - Response data
     */
    async put(url, data, options = {}) {
        return this.request('PUT', url, data, options);
    }
    
    /**
     * Make a PATCH request
     * 
     * @param {string} url - The URL to request
     * @param {Object} data - The data to send
     * @param {Object} [options] - Request options
     * @param {Object} [options.headers] - Additional headers
     * @param {number} [options.timeout] - Request timeout
     * @param {boolean} [options.withCredentials] - Whether to include credentials
     * @returns {Promise<Object>} - Response data
     */
    async patch(url, data, options = {}) {
        return this.request('PATCH', url, data, options);
    }
    
    /**
     * Make a DELETE request
     * 
     * @param {string} url - The URL to request
     * @param {Object} [options] - Request options
     * @param {Object} [options.headers] - Additional headers
     * @param {number} [options.timeout] - Request timeout
     * @param {boolean} [options.withCredentials] - Whether to include credentials
     * @returns {Promise<Object>} - Response data
     */
    async delete(url, options = {}) {
        return this.request('DELETE', url, null, options);
    }
    
    /**
     * Make an HTTP request
     * 
     * @param {string} method - The HTTP method
     * @param {string} url - The URL to request
     * @param {Object} [data] - The data to send
     * @param {Object} [options] - Request options
     * @param {Object} [options.headers] - Additional headers
     * @param {number} [options.timeout] - Request timeout
     * @param {boolean} [options.withCredentials] - Whether to include credentials
     * @returns {Promise<Object>} - Response data
     */
    async request(method, url, data = null, options = {}) {
        const fullUrl = this.baseUrl ? `${this.baseUrl}${url}` : url;
        const headers = { ...this.defaultHeaders, ...options.headers };
        const timeout = options.timeout || this.timeout;
        const withCredentials = options.withCredentials !== undefined ? options.withCredentials : this.withCredentials;
        
        // Create abort controller for timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), timeout);
        
        try {
            const fetchOptions = {
                method,
                headers,
                credentials: withCredentials ? 'include' : 'same-origin',
                signal: controller.signal
            };
            
            // Add body for non-GET requests
            if (data && method !== 'GET') {
                fetchOptions.body = JSON.stringify(data);
            }
            
            const response = await fetch(fullUrl, fetchOptions);
            
            // Clear timeout
            clearTimeout(timeoutId);
            
            // Parse response
            let responseData;
            const contentType = response.headers.get('content-type');
            
            if (contentType && contentType.includes('application/json')) {
                responseData = await response.json();
            } else {
                responseData = await response.text();
            }
            
            // Handle error responses
            if (!response.ok) {
                throw new HttpError(
                    response.statusText,
                    response.status,
                    responseData
                );
            }
            
            return responseData;
        } catch (error) {
            // Clear timeout
            clearTimeout(timeoutId);
            
            // Handle abort error (timeout)
            if (error.name === 'AbortError') {
                throw new HttpError('Request timeout', 408);
            }
            
            // Re-throw HttpError
            if (error instanceof HttpError) {
                throw error;
            }
            
            // Wrap other errors
            throw new HttpError(error.message, 0);
        }
    }
}

/**
 * HttpError class
 * 
 * Custom error class for HTTP errors
 */
class HttpError extends Error {
    /**
     * Constructor
     * 
     * @param {string} message - Error message
     * @param {number} status - HTTP status code
     * @param {*} [data] - Response data
     */
    constructor(message, status, data = null) {
        super(message);
        this.name = 'HttpError';
        this.status = status;
        this.data = data;
    }
}

// Create a singleton instance
const httpClient = new HttpClient();

// Export the HttpClient class and singleton instance
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        HttpClient,
        httpClient,
        HttpError
    };
} else {
    window.HttpClient = HttpClient;
    window.httpClient = httpClient;
    window.HttpError = HttpError;
}
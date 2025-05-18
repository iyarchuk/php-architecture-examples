<?php

namespace MonolithicArchitecture\Utils;

/**
 * Validator utility for validating data
 */
class Validator
{
    /**
     * Validate email format
     * 
     * @param string $email Email to validate
     * @return bool True if valid, false otherwise
     */
    public function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate password strength
     * 
     * @param string $password Password to validate
     * @return bool True if strong enough, false otherwise
     */
    public function isStrongPassword(string $password): bool
    {
        // Check if password is at least 8 characters long
        if (strlen($password) < 8) {
            return false;
        }

        // Check if password contains at least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }

        // Check if password contains at least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }

        // Check if password contains at least one digit
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }

        // Check if password contains at least one special character
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return false;
        }

        return true;
    }

    /**
     * Validate product data
     * 
     * @param array $data Product data to validate
     * @return bool True if valid, false otherwise
     */
    public function isValidProductData(array $data): bool
    {
        // Check if required fields are present
        if (!isset($data['name']) || !isset($data['price'])) {
            return false;
        }

        // Check if name is not empty
        if (empty($data['name'])) {
            return false;
        }

        // Check if price is a positive number
        if (!is_numeric($data['price']) || $data['price'] <= 0) {
            return false;
        }

        return true;
    }

    /**
     * Validate order data
     * 
     * @param array $data Order data to validate
     * @return bool True if valid, false otherwise
     */
    public function isValidOrderData(array $data): bool
    {
        // Check if required fields are present
        if (!isset($data['user_id']) || !isset($data['items']) || !is_array($data['items'])) {
            return false;
        }

        // Check if user_id is a positive integer
        if (!is_int($data['user_id']) || $data['user_id'] <= 0) {
            return false;
        }

        // Check if items array is not empty
        if (empty($data['items'])) {
            return false;
        }

        // Validate each item in the order
        foreach ($data['items'] as $item) {
            if (!$this->isValidOrderItem($item)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate order item data
     * 
     * @param array $data Order item data to validate
     * @return bool True if valid, false otherwise
     */
    private function isValidOrderItem(array $data): bool
    {
        // Check if required fields are present
        if (!isset($data['product_id']) || !isset($data['quantity'])) {
            return false;
        }

        // Check if product_id is a positive integer
        if (!is_int($data['product_id']) || $data['product_id'] <= 0) {
            return false;
        }

        // Check if quantity is a positive integer
        if (!is_int($data['quantity']) || $data['quantity'] <= 0) {
            return false;
        }

        return true;
    }

    /**
     * Validate payment method data
     * 
     * @param array $data Payment method data to validate
     * @return bool True if valid, false otherwise
     */
    public function isValidPaymentMethodData(array $data): bool
    {
        // Check if required fields are present
        if (!isset($data['type'])) {
            return false;
        }

        // Check if type is one of the allowed values
        $allowedTypes = ['credit_card', 'debit_card', 'paypal', 'bank_transfer'];
        if (!in_array($data['type'], $allowedTypes)) {
            return false;
        }

        // Validate specific fields based on payment method type
        switch ($data['type']) {
            case 'credit_card':
            case 'debit_card':
                // Check if card number is present and valid
                if (!isset($data['card_number']) || !$this->isValidCardNumber($data['card_number'])) {
                    return false;
                }
                
                // Check if card expiry is present and valid
                if (!isset($data['card_expiry']) || !$this->isValidCardExpiry($data['card_expiry'])) {
                    return false;
                }
                
                // Check if CVV is present and valid
                if (!isset($data['card_cvv']) || !$this->isValidCardCVV($data['card_cvv'])) {
                    return false;
                }
                break;
                
            case 'paypal':
                // Check if PayPal email is present and valid
                if (!isset($data['paypal_email']) || !$this->isValidEmail($data['paypal_email'])) {
                    return false;
                }
                break;
                
            case 'bank_transfer':
                // Check if bank account is present
                if (!isset($data['bank_account']) || empty($data['bank_account'])) {
                    return false;
                }
                
                // Check if bank name is present
                if (!isset($data['bank_name']) || empty($data['bank_name'])) {
                    return false;
                }
                break;
        }

        return true;
    }

    /**
     * Validate credit card number
     * 
     * @param string $cardNumber Card number to validate
     * @return bool True if valid, false otherwise
     */
    private function isValidCardNumber(string $cardNumber): bool
    {
        // Remove spaces and dashes
        $cardNumber = str_replace([' ', '-'], '', $cardNumber);
        
        // Check if card number contains only digits
        if (!ctype_digit($cardNumber)) {
            return false;
        }
        
        // Check if card number length is valid (13-19 digits)
        $length = strlen($cardNumber);
        if ($length < 13 || $length > 19) {
            return false;
        }
        
        // Implement Luhn algorithm for card number validation
        $sum = 0;
        $doubleUp = false;
        
        for ($i = $length - 1; $i >= 0; $i--) {
            $digit = (int) $cardNumber[$i];
            
            if ($doubleUp) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            
            $sum += $digit;
            $doubleUp = !$doubleUp;
        }
        
        return ($sum % 10) === 0;
    }

    /**
     * Validate credit card expiry date
     * 
     * @param string $expiry Expiry date to validate (MM/YY format)
     * @return bool True if valid, false otherwise
     */
    private function isValidCardExpiry(string $expiry): bool
    {
        // Check if expiry date matches MM/YY format
        if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $expiry, $matches)) {
            return false;
        }
        
        $month = (int) $matches[1];
        $year = (int) $matches[2] + 2000; // Convert YY to YYYY
        
        // Get current date
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('m');
        
        // Check if expiry date is in the future
        if ($year < $currentYear || ($year === $currentYear && $month < $currentMonth)) {
            return false;
        }
        
        return true;
    }

    /**
     * Validate credit card CVV
     * 
     * @param string $cvv CVV to validate
     * @return bool True if valid, false otherwise
     */
    private function isValidCardCVV(string $cvv): bool
    {
        // Check if CVV contains only digits
        if (!ctype_digit($cvv)) {
            return false;
        }
        
        // Check if CVV length is valid (3-4 digits)
        $length = strlen($cvv);
        if ($length < 3 || $length > 4) {
            return false;
        }
        
        return true;
    }
}
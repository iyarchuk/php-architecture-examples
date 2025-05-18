<?php

namespace BlackboardArchitecture\KnowledgeSources;

use BlackboardArchitecture\Blackboard\Blackboard;

/**
 * PasswordStrengthChecker
 * 
 * This class represents a knowledge source that checks the strength of passwords.
 * It analyzes password complexity, identifies weaknesses, and provides feedback.
 */
class PasswordStrengthChecker extends KnowledgeSource
{
    /**
     * @var int The minimum length of a valid password
     */
    private int $minLength;
    
    /**
     * @var array Common passwords to check against
     */
    private array $commonPasswords = [
        'password', '123456', 'qwerty', 'admin', 'welcome',
        'password123', '12345678', 'abc123', 'letmein', 'monkey'
    ];
    
    /**
     * Constructor
     * 
     * @param int $minLength The minimum length of a valid password
     */
    public function __construct(int $minLength = 8)
    {
        parent::__construct('PasswordStrengthChecker');
        $this->minLength = $minLength;
    }
    
    /**
     * Check if the knowledge source can contribute to the current state of the blackboard
     * 
     * @param Blackboard $blackboard The blackboard
     * @return bool True if the knowledge source can contribute, false otherwise
     */
    public function canContribute(Blackboard $blackboard): bool
    {
        // Can contribute if there's a password to check and it hasn't been checked yet
        return $blackboard->has('password') && !$blackboard->has('password_checked');
    }
    
    /**
     * Contribute to the blackboard
     * 
     * @param Blackboard $blackboard The blackboard
     * @return void
     */
    public function contribute(Blackboard $blackboard): void
    {
        $password = $blackboard->get('password');
        
        $this->log($blackboard, "Checking password strength");
        
        // Check password length
        if (strlen($password) < $this->minLength) {
            $blackboard->set('password_valid', false, $this->name);
            $blackboard->set('password_error', "Password is too short (minimum {$this->minLength} characters)", $this->name);
            $blackboard->set('password_checked', true, $this->name);
            $blackboard->set('password_strength', 'weak', $this->name);
            $this->log($blackboard, "Password is too short");
            return;
        }
        
        // Check if password is common
        if (in_array(strtolower($password), $this->commonPasswords)) {
            $blackboard->set('password_valid', false, $this->name);
            $blackboard->set('password_error', "Password is too common", $this->name);
            $blackboard->set('password_checked', true, $this->name);
            $blackboard->set('password_strength', 'weak', $this->name);
            $this->log($blackboard, "Password is too common");
            return;
        }
        
        // Check for personal information in password
        if ($blackboard->has('first_name') && stripos($password, $blackboard->get('first_name')) !== false) {
            $blackboard->set('password_warning', "Password contains your first name", $this->name);
            $this->log($blackboard, "Password contains first name");
        }
        
        if ($blackboard->has('last_name') && stripos($password, $blackboard->get('last_name')) !== false) {
            $blackboard->set('password_warning', "Password contains your last name", $this->name);
            $this->log($blackboard, "Password contains last name");
        }
        
        if ($blackboard->has('email')) {
            $emailParts = explode('@', $blackboard->get('email'));
            if (stripos($password, $emailParts[0]) !== false) {
                $blackboard->set('password_warning', "Password contains part of your email", $this->name);
                $this->log($blackboard, "Password contains email username");
            }
        }
        
        // Calculate password strength
        $strength = $this->calculatePasswordStrength($password);
        $strengthLabel = $this->getStrengthLabel($strength);
        
        $blackboard->set('password_strength_score', $strength, $this->name);
        $blackboard->set('password_strength', $strengthLabel, $this->name);
        
        // Set password as valid if it's not weak
        $isValid = $strengthLabel !== 'weak';
        $blackboard->set('password_valid', $isValid, $this->name);
        
        if (!$isValid) {
            $blackboard->set('password_error', "Password is too weak", $this->name);
        }
        
        // Provide feedback based on strength
        $feedback = $this->getPasswordFeedback($password);
        if (!empty($feedback)) {
            $blackboard->set('password_feedback', $feedback, $this->name);
        }
        
        $blackboard->set('password_checked', true, $this->name);
        $this->log($blackboard, "Password strength: $strengthLabel (score: $strength)");
    }
    
    /**
     * Calculate the strength of a password
     * 
     * @param string $password The password to check
     * @return int The strength score (0-100)
     */
    private function calculatePasswordStrength(string $password): int
    {
        $score = 0;
        
        // Length contribution (up to 40 points)
        $length = strlen($password);
        $score += min(40, $length * 4);
        
        // Character variety contribution (up to 60 points)
        if (preg_match('/[a-z]/', $password)) $score += 10; // Lowercase
        if (preg_match('/[A-Z]/', $password)) $score += 10; // Uppercase
        if (preg_match('/[0-9]/', $password)) $score += 10; // Numbers
        if (preg_match('/[^a-zA-Z0-9]/', $password)) $score += 15; // Special chars
        
        // Variety of characters
        $uniqueChars = count(array_unique(str_split($password)));
        $score += min(15, $uniqueChars * 2);
        
        // Penalize repetitions
        if (preg_match('/(.)\\1{2,}/', $password)) {
            $score -= 10;
        }
        
        // Ensure score is between 0 and 100
        return max(0, min(100, $score));
    }
    
    /**
     * Get a label for a password strength score
     * 
     * @param int $score The strength score
     * @return string The strength label
     */
    private function getStrengthLabel(int $score): string
    {
        if ($score < 40) return 'weak';
        if ($score < 70) return 'moderate';
        if ($score < 90) return 'strong';
        return 'very strong';
    }
    
    /**
     * Get feedback for improving a password
     * 
     * @param string $password The password to check
     * @return string The feedback
     */
    private function getPasswordFeedback(string $password): string
    {
        $feedback = [];
        
        if (strlen($password) < 12) {
            $feedback[] = "Consider using a longer password (at least 12 characters).";
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $feedback[] = "Add lowercase letters.";
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $feedback[] = "Add uppercase letters.";
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $feedback[] = "Add numbers.";
        }
        
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $feedback[] = "Add special characters.";
        }
        
        return implode(' ', $feedback);
    }
    
    /**
     * Get the priority of the knowledge source
     * 
     * @return int
     */
    public function getPriority(): int
    {
        return 5; // Medium priority
    }
}
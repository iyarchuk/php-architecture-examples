<?php

namespace BlackboardArchitecture\KnowledgeSources;

use BlackboardArchitecture\Blackboard\Blackboard;

/**
 * UserProfileCompleter
 * 
 * This class represents a knowledge source that completes user profiles.
 * It aggregates information from other knowledge sources, generates additional profile data,
 * and determines if the registration process is complete.
 */
class UserProfileCompleter extends KnowledgeSource
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('UserProfileCompleter');
    }
    
    /**
     * Check if the knowledge source can contribute to the current state of the blackboard
     * 
     * @param Blackboard $blackboard The blackboard
     * @return bool True if the knowledge source can contribute, false otherwise
     */
    public function canContribute(Blackboard $blackboard): bool
    {
        // Can contribute if basic user information is available and validated
        $hasBasicInfo = $blackboard->has('name') && $blackboard->has('email') && $blackboard->has('password');
        $isValidated = $blackboard->has('name_analyzed') && $blackboard->has('email_validated') && $blackboard->has('password_checked');
        $notCompleted = !$blackboard->has('profile_completed');
        
        return $hasBasicInfo && $isValidated && $notCompleted;
    }
    
    /**
     * Contribute to the blackboard
     * 
     * @param Blackboard $blackboard The blackboard
     * @return void
     */
    public function contribute(Blackboard $blackboard): void
    {
        $this->log($blackboard, "Completing user profile");
        
        // Check if all validations passed
        $nameValid = $blackboard->get('name_valid', false);
        $emailValid = $blackboard->get('email_valid', false);
        $passwordValid = $blackboard->get('password_valid', false);
        
        $allValid = $nameValid && $emailValid && $passwordValid;
        
        if (!$allValid) {
            $errors = [];
            
            if (!$nameValid) {
                $errors[] = $blackboard->get('name_error', 'Invalid name');
            }
            
            if (!$emailValid) {
                $errors[] = $blackboard->get('email_error', 'Invalid email');
            }
            
            if (!$passwordValid) {
                $errors[] = $blackboard->get('password_error', 'Invalid password');
            }
            
            $blackboard->set('registration_errors', $errors, $this->name);
            $blackboard->set('profile_completed', false, $this->name);
            $blackboard->set('registration_status', 'failed', $this->name);
            
            $this->log($blackboard, "Profile completion failed due to validation errors");
            return;
        }
        
        // Generate username if not already set
        if (!$blackboard->has('username')) {
            $username = $this->generateUsername($blackboard);
            $blackboard->set('username', $username, $this->name);
            $this->log($blackboard, "Generated username: $username");
        }
        
        // Set default user role
        $blackboard->set('user_role', 'member', $this->name);
        
        // Set registration date
        $blackboard->set('registration_date', date('Y-m-d H:i:s'), $this->name);
        
        // Generate avatar URL based on email (e.g., Gravatar)
        $email = $blackboard->get('email');
        $avatarUrl = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . '?d=mp';
        $blackboard->set('avatar_url', $avatarUrl, $this->name);
        
        // Determine account status based on email domain
        $emailDomain = $blackboard->get('email_domain', '');
        $isCommonDomain = $blackboard->get('email_common_domain', false);
        
        if ($isCommonDomain) {
            $blackboard->set('account_status', 'active', $this->name);
            $blackboard->set('email_verification_required', true, $this->name);
        } else {
            $blackboard->set('account_status', 'pending', $this->name);
            $blackboard->set('email_verification_required', true, $this->name);
            $blackboard->set('manual_review_required', true, $this->name);
            $this->log($blackboard, "Manual review required due to non-common email domain");
        }
        
        // Collect all profile data
        $profileData = [
            'name' => $blackboard->get('name'),
            'email' => $blackboard->get('email'),
            'username' => $blackboard->get('username'),
            'first_name' => $blackboard->get('first_name', ''),
            'last_name' => $blackboard->get('last_name', ''),
            'avatar_url' => $avatarUrl,
            'user_role' => 'member',
            'account_status' => $blackboard->get('account_status'),
            'registration_date' => $blackboard->get('registration_date'),
            'email_verification_required' => $blackboard->get('email_verification_required', true),
            'password_strength' => $blackboard->get('password_strength', 'unknown')
        ];
        
        // Set the complete profile
        $blackboard->set('user_profile', $profileData, $this->name);
        $blackboard->set('profile_completed', true, $this->name);
        $blackboard->set('registration_status', 'success', $this->name);
        
        $this->log($blackboard, "User profile completed successfully");
        
        // Add recommendations based on profile
        $recommendations = [];
        
        if ($blackboard->get('password_strength') !== 'very strong') {
            $recommendations[] = "Consider updating your password to a stronger one.";
        }
        
        if ($blackboard->has('password_feedback')) {
            $recommendations[] = $blackboard->get('password_feedback');
        }
        
        if (!empty($recommendations)) {
            $blackboard->set('user_recommendations', $recommendations, $this->name);
        }
    }
    
    /**
     * Generate a username based on available information
     * 
     * @param Blackboard $blackboard The blackboard
     * @return string The generated username
     */
    private function generateUsername(Blackboard $blackboard): string
    {
        $firstName = $blackboard->get('first_name', '');
        $lastName = $blackboard->get('last_name', '');
        $email = $blackboard->get('email', '');
        
        // Try to generate from first and last name
        if (!empty($firstName) && !empty($lastName)) {
            return strtolower($firstName . '.' . $lastName) . rand(1, 99);
        }
        
        // Try to generate from first name only
        if (!empty($firstName)) {
            return strtolower($firstName) . rand(100, 999);
        }
        
        // Generate from email
        $emailParts = explode('@', $email);
        $emailUsername = $emailParts[0];
        
        // Clean up email username
        $emailUsername = preg_replace('/[^a-z0-9]/', '', strtolower($emailUsername));
        
        // Add random numbers if too short
        if (strlen($emailUsername) < 5) {
            $emailUsername .= rand(1000, 9999);
        }
        
        return $emailUsername;
    }
    
    /**
     * Get the priority of the knowledge source
     * 
     * @return int
     */
    public function getPriority(): int
    {
        return 1; // Low priority - should run after all validations
    }
}
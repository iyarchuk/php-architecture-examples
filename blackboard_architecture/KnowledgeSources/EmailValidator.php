<?php

namespace BlackboardArchitecture\KnowledgeSources;

use BlackboardArchitecture\Blackboard\Blackboard;

/**
 * EmailValidator
 * 
 * This class represents a knowledge source that validates email addresses.
 * It checks if the email is valid, extracts domain information, and provides feedback.
 */
class EmailValidator extends KnowledgeSource
{
    /**
     * @var array List of common email domains
     */
    private array $commonDomains = [
        'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'aol.com',
        'icloud.com', 'protonmail.com', 'mail.com', 'zoho.com'
    ];
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('EmailValidator');
    }
    
    /**
     * Check if the knowledge source can contribute to the current state of the blackboard
     * 
     * @param Blackboard $blackboard The blackboard
     * @return bool True if the knowledge source can contribute, false otherwise
     */
    public function canContribute(Blackboard $blackboard): bool
    {
        // Can contribute if there's an email to validate and it hasn't been validated yet
        return $blackboard->has('email') && !$blackboard->has('email_validated');
    }
    
    /**
     * Contribute to the blackboard
     * 
     * @param Blackboard $blackboard The blackboard
     * @return void
     */
    public function contribute(Blackboard $blackboard): void
    {
        $email = $blackboard->get('email');
        
        $this->log($blackboard, "Validating email: $email");
        
        // Basic validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $blackboard->set('email_valid', false, $this->name);
            $blackboard->set('email_error', "Invalid email format", $this->name);
            $blackboard->set('email_validated', true, $this->name);
            $this->log($blackboard, "Email has invalid format");
            return;
        }
        
        // Extract domain information
        $domain = substr(strrchr($email, "@"), 1);
        $blackboard->set('email_domain', $domain, $this->name);
        
        // Check if domain is common
        $isCommonDomain = in_array($domain, $this->commonDomains);
        $blackboard->set('email_common_domain', $isCommonDomain, $this->name);
        
        // Check for disposable email domains (simplified check)
        $disposableDomains = ['mailinator.com', 'tempmail.com', 'throwawaymail.com', 'yopmail.com'];
        $isDisposable = in_array($domain, $disposableDomains);
        
        if ($isDisposable) {
            $blackboard->set('email_valid', false, $this->name);
            $blackboard->set('email_error', "Disposable email addresses are not allowed", $this->name);
            $blackboard->set('email_validated', true, $this->name);
            $this->log($blackboard, "Email is from a disposable domain");
            return;
        }
        
        // Check for typos in common domains (simplified check)
        if (!$isCommonDomain) {
            foreach ($this->commonDomains as $commonDomain) {
                $distance = levenshtein($domain, $commonDomain);
                if ($distance === 1) {
                    $blackboard->set('email_suggestion', "Did you mean $commonDomain?", $this->name);
                    $this->log($blackboard, "Possible typo in email domain");
                    break;
                }
            }
        }
        
        // Set email as valid
        $blackboard->set('email_valid', true, $this->name);
        $blackboard->set('email_validated', true, $this->name);
        
        // Provide feedback based on domain
        if ($isCommonDomain) {
            $this->log($blackboard, "Email is valid with a common domain");
        } else {
            $this->log($blackboard, "Email is valid with a non-common domain");
        }
    }
    
    /**
     * Get the priority of the knowledge source
     * 
     * @return int
     */
    public function getPriority(): int
    {
        return 8; // Medium-high priority - email validation is important
    }
}
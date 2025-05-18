<?php

namespace MonolithicArchitecture\Utils;

/**
 * EmailSender provides functionality for sending emails
 */
class EmailSender
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    /**
     * Send an email
     * 
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $body Email body
     * @return bool Whether the email was sent successfully
     */
    public function send(string $to, string $subject, string $body): bool
    {
        // Log the email sending attempt
        $this->logger->info("Sending email to: $to with subject: $subject");

        // In a real application, we would use a mail library or service
        // For this example, we'll just simulate sending an email
        
        // Validate email format
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->logger->error("Invalid email format: $to");
            return false;
        }

        // Simulate email sending
        // In a real application, this would use mail(), PHPMailer, or an email service API
        
        // Log success
        $this->logger->info("Email sent successfully to: $to");
        
        return true;
    }
}
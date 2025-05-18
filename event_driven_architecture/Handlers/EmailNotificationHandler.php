<?php

namespace EventDrivenArchitecture\Handlers;

/**
 * EmailNotificationHandler
 * 
 * This class is responsible for sending email notifications.
 * In a real application, this would use a mail service or library.
 */
class EmailNotificationHandler
{
    /**
     * @var bool Whether to actually send emails or just simulate
     */
    private bool $simulate;
    
    /**
     * @var array Log of sent emails
     */
    private array $sentEmails = [];
    
    /**
     * Constructor
     * 
     * @param bool $simulate Whether to simulate sending emails
     */
    public function __construct(bool $simulate = true)
    {
        $this->simulate = $simulate;
    }
    
    /**
     * Send a welcome email to a new user
     * 
     * @param int $userId
     * @param string $userName
     * @param string $userEmail
     * @return bool
     */
    public function sendWelcomeEmail(int $userId, string $userName, string $userEmail): bool
    {
        $subject = "Welcome to our platform, $userName!";
        $body = "Dear $userName,\n\n"
              . "Welcome to our platform! We're excited to have you on board.\n\n"
              . "Your account has been created successfully.\n\n"
              . "Best regards,\nThe Team";
        
        return $this->sendEmail($userEmail, $subject, $body, [
            'type' => 'welcome',
            'user_id' => $userId
        ]);
    }
    
    /**
     * Send an email notification when a user's email is changed
     * 
     * @param int $userId
     * @param string $userName
     * @param string $userEmail
     * @return bool
     */
    public function sendEmailChangedNotification(int $userId, string $userName, string $userEmail): bool
    {
        $subject = "Email Address Updated";
        $body = "Dear $userName,\n\n"
              . "Your email address has been updated to $userEmail.\n\n"
              . "If you did not make this change, please contact support immediately.\n\n"
              . "Best regards,\nThe Team";
        
        return $this->sendEmail($userEmail, $subject, $body, [
            'type' => 'email_changed',
            'user_id' => $userId
        ]);
    }
    
    /**
     * Send an account deleted email
     * 
     * @param int $userId
     * @param string $userName
     * @param string $userEmail
     * @param string|null $reason
     * @return bool
     */
    public function sendAccountDeletedEmail(int $userId, string $userName, string $userEmail, ?string $reason = null): bool
    {
        $subject = "Account Deleted";
        $body = "Dear $userName,\n\n"
              . "Your account has been deleted from our platform.\n\n";
        
        if ($reason) {
            $body .= "Reason: $reason\n\n";
        }
        
        $body .= "We're sorry to see you go. If you wish to rejoin in the future, you'll need to create a new account.\n\n"
               . "Best regards,\nThe Team";
        
        return $this->sendEmail($userEmail, $subject, $body, [
            'type' => 'account_deleted',
            'user_id' => $userId,
            'reason' => $reason
        ]);
    }
    
    /**
     * Send an email
     * 
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param array $metadata
     * @return bool
     */
    private function sendEmail(string $to, string $subject, string $body, array $metadata = []): bool
    {
        // Log the email
        $email = [
            'to' => $to,
            'subject' => $subject,
            'body' => $body,
            'metadata' => $metadata,
            'sent_at' => new \DateTimeImmutable()
        ];
        
        $this->sentEmails[] = $email;
        
        // If simulating, just return true
        if ($this->simulate) {
            echo "Simulated sending email: '$subject' to $to\n";
            return true;
        }
        
        // In a real application, this would use a mail service or library
        // For example: mail($to, $subject, $body);
        
        return true;
    }
    
    /**
     * Get the log of sent emails
     * 
     * @return array
     */
    public function getSentEmails(): array
    {
        return $this->sentEmails;
    }
    
    /**
     * Clear the log of sent emails
     * 
     * @return void
     */
    public function clearSentEmails(): void
    {
        $this->sentEmails = [];
    }
}
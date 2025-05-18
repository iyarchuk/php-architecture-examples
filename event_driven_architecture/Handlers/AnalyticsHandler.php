<?php

namespace EventDrivenArchitecture\Handlers;

/**
 * AnalyticsHandler
 * 
 * This class is responsible for collecting analytics data from events.
 * In a real application, this would use an analytics service or database.
 */
class AnalyticsHandler
{
    /**
     * @var array User registration analytics
     */
    private array $userRegistrations = [];
    
    /**
     * @var array User update analytics
     */
    private array $userUpdates = [];
    
    /**
     * @var array User deletion analytics
     */
    private array $userDeletions = [];
    
    /**
     * Track a user registration
     * 
     * @param int $userId
     * @param \DateTimeImmutable $timestamp
     * @return void
     */
    public function trackUserRegistration(int $userId, \DateTimeImmutable $timestamp): void
    {
        $this->userRegistrations[] = [
            'user_id' => $userId,
            'timestamp' => $timestamp,
            'tracked_at' => new \DateTimeImmutable()
        ];
        
        echo "Analytics: Tracked user registration for user ID $userId\n";
    }
    
    /**
     * Track a user update
     * 
     * @param int $userId
     * @param array $updatedFields
     * @param \DateTimeImmutable $timestamp
     * @return void
     */
    public function trackUserUpdate(int $userId, array $updatedFields, \DateTimeImmutable $timestamp): void
    {
        $this->userUpdates[] = [
            'user_id' => $userId,
            'updated_fields' => $updatedFields,
            'timestamp' => $timestamp,
            'tracked_at' => new \DateTimeImmutable()
        ];
        
        echo "Analytics: Tracked user update for user ID $userId (fields: " . implode(', ', $updatedFields) . ")\n";
    }
    
    /**
     * Track a user deletion
     * 
     * @param int $userId
     * @param string|null $reason
     * @param \DateTimeImmutable $timestamp
     * @return void
     */
    public function trackUserDeletion(int $userId, ?string $reason, \DateTimeImmutable $timestamp): void
    {
        $this->userDeletions[] = [
            'user_id' => $userId,
            'reason' => $reason,
            'timestamp' => $timestamp,
            'tracked_at' => new \DateTimeImmutable()
        ];
        
        echo "Analytics: Tracked user deletion for user ID $userId" . ($reason ? " (reason: $reason)" : "") . "\n";
    }
    
    /**
     * Get user registration analytics
     * 
     * @return array
     */
    public function getUserRegistrations(): array
    {
        return $this->userRegistrations;
    }
    
    /**
     * Get user update analytics
     * 
     * @return array
     */
    public function getUserUpdates(): array
    {
        return $this->userUpdates;
    }
    
    /**
     * Get user deletion analytics
     * 
     * @return array
     */
    public function getUserDeletions(): array
    {
        return $this->userDeletions;
    }
    
    /**
     * Get all analytics data
     * 
     * @return array
     */
    public function getAllAnalytics(): array
    {
        return [
            'registrations' => $this->userRegistrations,
            'updates' => $this->userUpdates,
            'deletions' => $this->userDeletions
        ];
    }
    
    /**
     * Get analytics summary
     * 
     * @return array
     */
    public function getAnalyticsSummary(): array
    {
        return [
            'total_registrations' => count($this->userRegistrations),
            'total_updates' => count($this->userUpdates),
            'total_deletions' => count($this->userDeletions)
        ];
    }
    
    /**
     * Clear all analytics data
     * 
     * @return void
     */
    public function clearAnalytics(): void
    {
        $this->userRegistrations = [];
        $this->userUpdates = [];
        $this->userDeletions = [];
    }
}
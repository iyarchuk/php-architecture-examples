<?php

namespace EventDrivenArchitecture\Publishers;

use EventDrivenArchitecture\Events\Event;
use EventDrivenArchitecture\Events\UserCreatedEvent;
use EventDrivenArchitecture\Events\UserUpdatedEvent;
use EventDrivenArchitecture\Events\UserDeletedEvent;
use EventDrivenArchitecture\EventBus\EventBus;

/**
 * UserPublisher
 * 
 * This class is responsible for publishing user-related events.
 */
class UserPublisher implements Publisher
{
    /**
     * @var EventBus The event bus
     */
    private EventBus $eventBus;
    
    /**
     * Set the event bus
     * 
     * @param EventBus $eventBus
     * @return void
     */
    public function setEventBus(EventBus $eventBus): void
    {
        $this->eventBus = $eventBus;
    }
    
    /**
     * Publish an event to the event bus
     * 
     * @param Event $event
     * @return void
     * @throws \RuntimeException If the event bus is not set
     */
    public function publish(Event $event): void
    {
        if (!isset($this->eventBus)) {
            throw new \RuntimeException('Event bus not set');
        }
        
        $this->eventBus->dispatch($event);
    }
    
    /**
     * Publish a user created event
     * 
     * @param int $userId
     * @param string $userName
     * @param string $userEmail
     * @return void
     */
    public function publishUserCreated(int $userId, string $userName, string $userEmail): void
    {
        $event = new UserCreatedEvent($userId, $userName, $userEmail);
        $this->publish($event);
    }
    
    /**
     * Publish a user updated event
     * 
     * @param int $userId
     * @param string $userName
     * @param string $userEmail
     * @param array $updatedFields
     * @return void
     */
    public function publishUserUpdated(int $userId, string $userName, string $userEmail, array $updatedFields): void
    {
        $event = new UserUpdatedEvent($userId, $userName, $userEmail, $updatedFields);
        $this->publish($event);
    }
    
    /**
     * Publish a user deleted event
     * 
     * @param int $userId
     * @param string $userName
     * @param string $userEmail
     * @param string|null $reason
     * @return void
     */
    public function publishUserDeleted(int $userId, string $userName, string $userEmail, ?string $reason = null): void
    {
        $event = new UserDeletedEvent($userId, $userName, $userEmail, $reason);
        $this->publish($event);
    }
}
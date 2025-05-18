<?php

namespace EventDrivenArchitecture\Subscribers;

use EventDrivenArchitecture\Events\Event;
use EventDrivenArchitecture\Events\UserCreatedEvent;
use EventDrivenArchitecture\Events\UserUpdatedEvent;
use EventDrivenArchitecture\Events\UserDeletedEvent;
use EventDrivenArchitecture\Handlers\EmailNotificationHandler;

/**
 * EmailNotifier
 * 
 * This class is responsible for sending email notifications when certain events occur.
 */
class EmailNotifier implements Subscriber
{
    /**
     * @var EmailNotificationHandler The email notification handler
     */
    private EmailNotificationHandler $handler;
    
    /**
     * Constructor
     * 
     * @param EmailNotificationHandler $handler
     */
    public function __construct(EmailNotificationHandler $handler)
    {
        $this->handler = $handler;
    }
    
    /**
     * Get the event types that this subscriber is interested in
     * 
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return [
            'user.created',
            'user.updated',
            'user.deleted'
        ];
    }
    
    /**
     * Handle an event
     * 
     * @param Event $event
     * @return void
     */
    public function handle(Event $event): void
    {
        if ($event instanceof UserCreatedEvent) {
            $this->handleUserCreated($event);
        } elseif ($event instanceof UserUpdatedEvent) {
            $this->handleUserUpdated($event);
        } elseif ($event instanceof UserDeletedEvent) {
            $this->handleUserDeleted($event);
        }
    }
    
    /**
     * Check if this subscriber can handle the given event
     * 
     * @param Event $event
     * @return bool
     */
    public function canHandle(Event $event): bool
    {
        return in_array($event->getName(), $this->getSubscribedEvents());
    }
    
    /**
     * Handle a user created event
     * 
     * @param UserCreatedEvent $event
     * @return void
     */
    private function handleUserCreated(UserCreatedEvent $event): void
    {
        $this->handler->sendWelcomeEmail(
            $event->getUserId(),
            $event->getUserName(),
            $event->getUserEmail()
        );
    }
    
    /**
     * Handle a user updated event
     * 
     * @param UserUpdatedEvent $event
     * @return void
     */
    private function handleUserUpdated(UserUpdatedEvent $event): void
    {
        if ($event->wasFieldUpdated('email')) {
            $this->handler->sendEmailChangedNotification(
                $event->getUserId(),
                $event->getUserName(),
                $event->getUserEmail()
            );
        }
    }
    
    /**
     * Handle a user deleted event
     * 
     * @param UserDeletedEvent $event
     * @return void
     */
    private function handleUserDeleted(UserDeletedEvent $event): void
    {
        $this->handler->sendAccountDeletedEmail(
            $event->getUserId(),
            $event->getUserName(),
            $event->getUserEmail(),
            $event->getReason()
        );
    }
}
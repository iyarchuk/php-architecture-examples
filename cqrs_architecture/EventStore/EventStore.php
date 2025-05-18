<?php

namespace CQRSArchitecture\EventStore;

use CQRSArchitecture\Models\UserReadModel;

/**
 * EventStore
 * 
 * This class is responsible for storing and retrieving events.
 * It also updates the read model based on the events.
 */
class EventStore
{
    /**
     * @var array The events stored in the event store
     */
    private static array $events = [];
    
    /**
     * @var array The event handlers
     */
    private static array $handlers = [];
    
    /**
     * Store an event
     * 
     * @param Event $event The event to store
     * @return void
     */
    public static function store(Event $event): void
    {
        // Store the event
        self::$events[] = $event;
        
        // Update the read model based on the event type
        switch ($event->getType()) {
            case 'user_created':
                if ($event instanceof UserCreatedEvent) {
                    UserReadModel::addUser(
                        $event->getUserId(),
                        $event->getName(),
                        $event->getEmail(),
                        $event->getCreatedAt()
                    );
                }
                break;
                
            case 'user_updated':
                if ($event instanceof UserUpdatedEvent) {
                    UserReadModel::updateUser(
                        $event->getUserId(),
                        $event->getChanges()
                    );
                }
                break;
                
            case 'user_deleted':
                if ($event instanceof UserDeletedEvent) {
                    UserReadModel::deleteUser($event->getUserId());
                }
                break;
        }
        
        // Notify handlers
        self::notifyHandlers($event);
    }
    
    /**
     * Store multiple events
     * 
     * @param array $events The events to store
     * @return void
     */
    public static function storeMany(array $events): void
    {
        foreach ($events as $event) {
            if ($event instanceof Event) {
                self::store($event);
            }
        }
    }
    
    /**
     * Get all events
     * 
     * @return array
     */
    public static function getAllEvents(): array
    {
        return self::$events;
    }
    
    /**
     * Get events by type
     * 
     * @param string $type The type of events to get
     * @return array
     */
    public static function getEventsByType(string $type): array
    {
        return array_filter(self::$events, function ($event) use ($type) {
            return $event->getType() === $type;
        });
    }
    
    /**
     * Get events for a specific user
     * 
     * @param int $userId The ID of the user
     * @return array
     */
    public static function getEventsForUser(int $userId): array
    {
        return array_filter(self::$events, function ($event) use ($userId) {
            if (method_exists($event, 'getUserId')) {
                return $event->getUserId() === $userId;
            }
            return false;
        });
    }
    
    /**
     * Register an event handler
     * 
     * @param string $eventType The type of event to handle
     * @param callable $handler The handler function
     * @return void
     */
    public static function registerHandler(string $eventType, callable $handler): void
    {
        if (!isset(self::$handlers[$eventType])) {
            self::$handlers[$eventType] = [];
        }
        
        self::$handlers[$eventType][] = $handler;
    }
    
    /**
     * Notify handlers of an event
     * 
     * @param Event $event The event to notify handlers about
     * @return void
     */
    private static function notifyHandlers(Event $event): void
    {
        $eventType = $event->getType();
        
        if (isset(self::$handlers[$eventType])) {
            foreach (self::$handlers[$eventType] as $handler) {
                $handler($event);
            }
        }
    }
    
    /**
     * Clear all events
     * 
     * @return void
     */
    public static function clear(): void
    {
        self::$events = [];
    }
}
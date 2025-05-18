<?php

namespace MicroservicesArchitecture\MessageBroker;

/**
 * Broker
 * 
 * This interface defines the contract for a message broker.
 * A message broker facilitates asynchronous communication between services,
 * enabling event-driven architecture and decoupling services.
 */
interface Broker
{
    /**
     * Publish a message to a topic
     * 
     * @param string $topic The topic to publish to
     * @param array $message The message to publish
     * @param array $headers Additional headers for the message
     * @return bool True if the message was published successfully
     */
    public function publish(string $topic, array $message, array $headers = []): bool;
    
    /**
     * Subscribe to a topic
     * 
     * @param string $topic The topic to subscribe to
     * @param callable $callback The callback to invoke when a message is received
     * @param string $subscriberId A unique identifier for the subscriber
     * @return bool True if the subscription was successful
     */
    public function subscribe(string $topic, callable $callback, string $subscriberId): bool;
    
    /**
     * Unsubscribe from a topic
     * 
     * @param string $topic The topic to unsubscribe from
     * @param string $subscriberId The identifier of the subscriber
     * @return bool True if the unsubscription was successful
     */
    public function unsubscribe(string $topic, string $subscriberId): bool;
    
    /**
     * Get all messages for a topic
     * 
     * @param string $topic The topic to get messages for
     * @return array An array of messages
     */
    public function getMessages(string $topic): array;
    
    /**
     * Get all subscribers for a topic
     * 
     * @param string $topic The topic to get subscribers for
     * @return array An array of subscriber IDs
     */
    public function getSubscribers(string $topic): array;
    
    /**
     * Get all topics
     * 
     * @return array An array of topics
     */
    public function getTopics(): array;
    
    /**
     * Process pending messages
     * 
     * @return int The number of messages processed
     */
    public function processMessages(): int;
}
<?php

namespace MicroservicesArchitecture\MessageBroker;

/**
 * InMemoryBroker
 * 
 * This class provides an in-memory implementation of the Broker interface.
 * It stores messages and subscriptions in memory and provides methods for
 * publishing, subscribing, and processing messages.
 */
class InMemoryBroker implements Broker
{
    /**
     * @var array The topics and their messages
     */
    private array $topics = [];
    
    /**
     * @var array The subscribers for each topic
     */
    private array $subscribers = [];
    
    /**
     * @var array The pending messages for each topic
     */
    private array $pendingMessages = [];
    
    /**
     * Publish a message to a topic
     * 
     * @param string $topic The topic to publish to
     * @param array $message The message to publish
     * @param array $headers Additional headers for the message
     * @return bool True if the message was published successfully
     */
    public function publish(string $topic, array $message, array $headers = []): bool
    {
        // Create the topic if it doesn't exist
        if (!isset($this->topics[$topic])) {
            $this->topics[$topic] = [];
        }
        
        // Create the pending messages array for the topic if it doesn't exist
        if (!isset($this->pendingMessages[$topic])) {
            $this->pendingMessages[$topic] = [];
        }
        
        // Add the message to the topic
        $messageId = uniqid('msg_', true);
        $messageData = [
            'id' => $messageId,
            'topic' => $topic,
            'message' => $message,
            'headers' => $headers,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        $this->topics[$topic][] = $messageData;
        
        // Add the message to the pending messages for each subscriber
        if (isset($this->subscribers[$topic])) {
            foreach ($this->subscribers[$topic] as $subscriberId => $callback) {
                $this->pendingMessages[$topic][] = [
                    'messageId' => $messageId,
                    'subscriberId' => $subscriberId,
                    'message' => $messageData
                ];
            }
        }
        
        return true;
    }
    
    /**
     * Subscribe to a topic
     * 
     * @param string $topic The topic to subscribe to
     * @param callable $callback The callback to invoke when a message is received
     * @param string $subscriberId A unique identifier for the subscriber
     * @return bool True if the subscription was successful
     */
    public function subscribe(string $topic, callable $callback, string $subscriberId): bool
    {
        // Create the topic if it doesn't exist
        if (!isset($this->topics[$topic])) {
            $this->topics[$topic] = [];
        }
        
        // Create the subscribers array for the topic if it doesn't exist
        if (!isset($this->subscribers[$topic])) {
            $this->subscribers[$topic] = [];
        }
        
        // Add the subscriber
        $this->subscribers[$topic][$subscriberId] = $callback;
        
        return true;
    }
    
    /**
     * Unsubscribe from a topic
     * 
     * @param string $topic The topic to unsubscribe from
     * @param string $subscriberId The identifier of the subscriber
     * @return bool True if the unsubscription was successful
     */
    public function unsubscribe(string $topic, string $subscriberId): bool
    {
        if (!isset($this->subscribers[$topic]) || !isset($this->subscribers[$topic][$subscriberId])) {
            return false;
        }
        
        // Remove the subscriber
        unset($this->subscribers[$topic][$subscriberId]);
        
        // If no more subscribers for the topic, remove the topic from subscribers
        if (empty($this->subscribers[$topic])) {
            unset($this->subscribers[$topic]);
        }
        
        // Remove any pending messages for this subscriber
        if (isset($this->pendingMessages[$topic])) {
            $this->pendingMessages[$topic] = array_filter(
                $this->pendingMessages[$topic],
                function ($pendingMessage) use ($subscriberId) {
                    return $pendingMessage['subscriberId'] !== $subscriberId;
                }
            );
            
            // If no more pending messages for the topic, remove the topic from pending messages
            if (empty($this->pendingMessages[$topic])) {
                unset($this->pendingMessages[$topic]);
            }
        }
        
        return true;
    }
    
    /**
     * Get all messages for a topic
     * 
     * @param string $topic The topic to get messages for
     * @return array An array of messages
     */
    public function getMessages(string $topic): array
    {
        return $this->topics[$topic] ?? [];
    }
    
    /**
     * Get all subscribers for a topic
     * 
     * @param string $topic The topic to get subscribers for
     * @return array An array of subscriber IDs
     */
    public function getSubscribers(string $topic): array
    {
        if (!isset($this->subscribers[$topic])) {
            return [];
        }
        
        return array_keys($this->subscribers[$topic]);
    }
    
    /**
     * Get all topics
     * 
     * @return array An array of topics
     */
    public function getTopics(): array
    {
        return array_keys($this->topics);
    }
    
    /**
     * Process pending messages
     * 
     * @return int The number of messages processed
     */
    public function processMessages(): int
    {
        $processed = 0;
        
        foreach ($this->pendingMessages as $topic => $messages) {
            foreach ($messages as $index => $pendingMessage) {
                $subscriberId = $pendingMessage['subscriberId'];
                $message = $pendingMessage['message'];
                
                // If the subscriber still exists, invoke the callback
                if (isset($this->subscribers[$topic][$subscriberId])) {
                    $callback = $this->subscribers[$topic][$subscriberId];
                    $callback($message);
                    $processed++;
                }
                
                // Remove the pending message
                unset($this->pendingMessages[$topic][$index]);
            }
            
            // If no more pending messages for the topic, remove the topic from pending messages
            if (empty($this->pendingMessages[$topic])) {
                unset($this->pendingMessages[$topic]);
            }
        }
        
        return $processed;
    }
}
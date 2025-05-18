<?php

namespace ClientServerArchitecture\Shared;

/**
 * Protocol
 * 
 * This class defines the communication protocol between the client and server.
 * It provides methods for encoding and decoding messages.
 */
class Protocol
{
    /**
     * @var string The protocol version
     */
    private const VERSION = '1.0';
    
    /**
     * @var string The delimiter used to separate the header and body
     */
    private const DELIMITER = "\r\n\r\n";
    
    /**
     * Encode a request or response into a protocol message
     * 
     * @param Request|Response $message
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function encode($message): string
    {
        if ($message instanceof Request) {
            $type = 'request';
            $body = $message->toJson();
        } elseif ($message instanceof Response) {
            $type = 'response';
            $body = $message->toJson();
        } else {
            throw new \InvalidArgumentException('Message must be a Request or Response');
        }
        
        $header = json_encode([
            'version' => self::VERSION,
            'type' => $type,
            'length' => strlen($body),
            'timestamp' => time()
        ]);
        
        return $header . self::DELIMITER . $body;
    }
    
    /**
     * Decode a protocol message into a request or response
     * 
     * @param string $message
     * @return Request|Response
     * @throws \InvalidArgumentException
     */
    public static function decode(string $message)
    {
        $parts = explode(self::DELIMITER, $message, 2);
        
        if (count($parts) !== 2) {
            throw new \InvalidArgumentException('Invalid message format');
        }
        
        $header = json_decode($parts[0], true);
        $body = $parts[1];
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid header format: ' . json_last_error_msg());
        }
        
        if (!isset($header['version']) || !isset($header['type']) || !isset($header['length'])) {
            throw new \InvalidArgumentException('Missing required header fields');
        }
        
        if ($header['version'] !== self::VERSION) {
            throw new \InvalidArgumentException("Unsupported protocol version: {$header['version']}");
        }
        
        if (strlen($body) !== $header['length']) {
            throw new \InvalidArgumentException('Body length mismatch');
        }
        
        if ($header['type'] === 'request') {
            return Request::fromJson($body);
        } elseif ($header['type'] === 'response') {
            return Response::fromJson($body);
        } else {
            throw new \InvalidArgumentException("Unknown message type: {$header['type']}");
        }
    }
    
    /**
     * Simulate sending a message over a network
     * 
     * In a real application, this would use sockets, HTTP, or another transport mechanism.
     * For this example, we're just returning the encoded message.
     * 
     * @param Request|Response $message
     * @return string
     */
    public static function send($message): string
    {
        return self::encode($message);
    }
    
    /**
     * Simulate receiving a message from a network
     * 
     * In a real application, this would use sockets, HTTP, or another transport mechanism.
     * For this example, we're just decoding the provided message.
     * 
     * @param string $encodedMessage
     * @return Request|Response
     */
    public static function receive(string $encodedMessage)
    {
        return self::decode($encodedMessage);
    }
}
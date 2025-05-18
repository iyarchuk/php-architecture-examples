<?php

namespace ServerlessArchitecture\Services;

/**
 * StorageService handles cloud storage operations in a serverless environment.
 * In a serverless architecture, cloud storage services like S3 are used for storing
 * files and data that need to persist beyond the function execution.
 */
class StorageService
{
    /**
     * @var array The configuration for the storage service
     */
    private $config;
    
    /**
     * @var array In-memory storage for this example
     */
    private $storage = [];
    
    /**
     * Constructor
     *
     * @param array $config The configuration for the storage service
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'bucketName' => 'serverless-example-bucket',
            'region' => 'us-east-1',
            'acl' => 'private'
        ], $config);
    }
    
    /**
     * Upload a file to the storage service
     *
     * @param string $key The key (path) where the file will be stored
     * @param string $content The content of the file
     * @param array $options Additional options for the upload
     * @return array The result of the upload operation
     */
    public function uploadFile(string $key, string $content, array $options = []): array
    {
        // In a real application, we would upload the file to a cloud storage service
        // For this example, we'll just store it in memory
        $this->storage[$key] = [
            'content' => $content,
            'metadata' => array_merge([
                'contentType' => 'application/octet-stream',
                'contentLength' => strlen($content),
                'lastModified' => date('Y-m-d H:i:s')
            ], $options)
        ];
        
        return [
            'success' => true,
            'message' => 'File uploaded successfully',
            'data' => [
                'key' => $key,
                'bucket' => $this->config['bucketName'],
                'url' => $this->getFileUrl($key),
                'metadata' => $this->storage[$key]['metadata']
            ]
        ];
    }
    
    /**
     * Download a file from the storage service
     *
     * @param string $key The key (path) of the file to download
     * @return array The result of the download operation
     */
    public function downloadFile(string $key): array
    {
        // Check if the file exists
        if (!isset($this->storage[$key])) {
            return [
                'success' => false,
                'message' => 'File not found'
            ];
        }
        
        return [
            'success' => true,
            'message' => 'File downloaded successfully',
            'data' => [
                'key' => $key,
                'bucket' => $this->config['bucketName'],
                'content' => $this->storage[$key]['content'],
                'metadata' => $this->storage[$key]['metadata']
            ]
        ];
    }
    
    /**
     * Delete a file from the storage service
     *
     * @param string $key The key (path) of the file to delete
     * @return array The result of the delete operation
     */
    public function deleteFile(string $key): array
    {
        // Check if the file exists
        if (!isset($this->storage[$key])) {
            return [
                'success' => false,
                'message' => 'File not found'
            ];
        }
        
        // Delete the file
        unset($this->storage[$key]);
        
        return [
            'success' => true,
            'message' => 'File deleted successfully',
            'data' => [
                'key' => $key,
                'bucket' => $this->config['bucketName']
            ]
        ];
    }
    
    /**
     * List files in the storage service
     *
     * @param string $prefix The prefix to filter files by
     * @return array The result of the list operation
     */
    public function listFiles(string $prefix = ''): array
    {
        $files = [];
        
        foreach ($this->storage as $key => $data) {
            if (empty($prefix) || strpos($key, $prefix) === 0) {
                $files[] = [
                    'key' => $key,
                    'url' => $this->getFileUrl($key),
                    'metadata' => $data['metadata']
                ];
            }
        }
        
        return [
            'success' => true,
            'message' => 'Files listed successfully',
            'data' => [
                'bucket' => $this->config['bucketName'],
                'prefix' => $prefix,
                'files' => $files
            ]
        ];
    }
    
    /**
     * Get the URL for a file
     *
     * @param string $key The key (path) of the file
     * @return string The URL of the file
     */
    private function getFileUrl(string $key): string
    {
        // In a real application, this would be the actual URL of the file in the cloud storage service
        return "https://{$this->config['bucketName']}.s3.{$this->config['region']}.amazonaws.com/{$key}";
    }
    
    /**
     * Get the configuration for the storage service
     *
     * @return array The configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
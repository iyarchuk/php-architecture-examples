<?php

namespace LayeredArchitecture\Database;

/**
 * Database Connection
 * 
 * This class handles the connection to the database.
 * In a real application, this would connect to a real database.
 * For simplicity, we're using an in-memory array to store data.
 */
class DatabaseConnection
{
    /**
     * @var array Simulated database tables
     */
    private array $tables = [];

    /**
     * @var DatabaseConnection|null Singleton instance
     */
    private static ?DatabaseConnection $instance = null;

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
        // Initialize tables
        $this->tables['users'] = [];
    }

    /**
     * Get the singleton instance
     * 
     * @return DatabaseConnection
     */
    public static function getInstance(): DatabaseConnection
    {
        if (self::$instance === null) {
            self::$instance = new DatabaseConnection();
        }
        return self::$instance;
    }

    /**
     * Insert a record into a table
     * 
     * @param string $table
     * @param array $data
     * @return int The ID of the inserted record
     */
    public function insert(string $table, array $data): int
    {
        if (!isset($this->tables[$table])) {
            $this->tables[$table] = [];
        }

        // Generate a new ID
        $id = count($this->tables[$table]) + 1;
        $data['id'] = $id;

        // Store the record
        $this->tables[$table][$id] = $data;

        return $id;
    }

    /**
     * Update a record in a table
     * 
     * @param string $table
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(string $table, int $id, array $data): bool
    {
        if (!isset($this->tables[$table][$id])) {
            return false;
        }

        // Preserve the ID
        $data['id'] = $id;

        // Update the record
        $this->tables[$table][$id] = $data;

        return true;
    }

    /**
     * Delete a record from a table
     * 
     * @param string $table
     * @param int $id
     * @return bool
     */
    public function delete(string $table, int $id): bool
    {
        if (!isset($this->tables[$table][$id])) {
            return false;
        }

        // Delete the record
        unset($this->tables[$table][$id]);

        return true;
    }

    /**
     * Find a record by ID
     * 
     * @param string $table
     * @param int $id
     * @return array|null
     */
    public function find(string $table, int $id): ?array
    {
        return $this->tables[$table][$id] ?? null;
    }

    /**
     * Find a record by a field value
     * 
     * @param string $table
     * @param string $field
     * @param mixed $value
     * @return array|null
     */
    public function findBy(string $table, string $field, $value): ?array
    {
        foreach ($this->tables[$table] as $record) {
            if (isset($record[$field]) && $record[$field] === $value) {
                return $record;
            }
        }
        return null;
    }

    /**
     * Find all records in a table
     * 
     * @param string $table
     * @return array
     */
    public function findAll(string $table): array
    {
        return $this->tables[$table] ?? [];
    }
}
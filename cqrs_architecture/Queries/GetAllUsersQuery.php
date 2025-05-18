<?php

namespace CQRSArchitecture\Queries;

/**
 * GetAllUsersQuery
 * 
 * This class represents a query to retrieve all users.
 * Queries are immutable and do not change the state of the system.
 */
class GetAllUsersQuery
{
    /**
     * @var array Optional filters for the query
     */
    private array $filters;
    
    /**
     * Constructor
     * 
     * @param array $filters Optional filters for the query
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }
    
    /**
     * Get the filters for the query
     * 
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }
    
    /**
     * Check if the query has a specific filter
     * 
     * @param string $key The filter key
     * @return bool
     */
    public function hasFilter(string $key): bool
    {
        return isset($this->filters[$key]);
    }
    
    /**
     * Get a specific filter value
     * 
     * @param string $key The filter key
     * @param mixed $default The default value if the filter doesn't exist
     * @return mixed
     */
    public function getFilter(string $key, $default = null)
    {
        return $this->filters[$key] ?? $default;
    }
}
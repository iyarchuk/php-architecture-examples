<?php

namespace CQRSArchitecture\Handlers\QueryHandlers;

use CQRSArchitecture\Queries\GetUserByIdQuery;
use CQRSArchitecture\Models\UserReadModel;

/**
 * GetUserByIdHandler
 * 
 * This class handles the GetUserByIdQuery.
 * It retrieves a user by ID from the read model.
 */
class GetUserByIdHandler
{
    /**
     * Handle the query
     * 
     * @param GetUserByIdQuery $query The query to handle
     * @return array|null The user data, or null if not found
     */
    public function handle(GetUserByIdQuery $query): ?array
    {
        return UserReadModel::getUserById($query->getUserId());
    }
}
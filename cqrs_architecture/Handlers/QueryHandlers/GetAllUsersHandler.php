<?php

namespace CQRSArchitecture\Handlers\QueryHandlers;

use CQRSArchitecture\Queries\GetAllUsersQuery;
use CQRSArchitecture\Models\UserReadModel;

/**
 * GetAllUsersHandler
 * 
 * This class handles the GetAllUsersQuery.
 * It retrieves all users from the read model.
 */
class GetAllUsersHandler
{
    /**
     * Handle the query
     * 
     * @param GetAllUsersQuery $query The query to handle
     * @return array The list of users
     */
    public function handle(GetAllUsersQuery $query): array
    {
        return UserReadModel::getAllUsers($query->getFilters());
    }
}
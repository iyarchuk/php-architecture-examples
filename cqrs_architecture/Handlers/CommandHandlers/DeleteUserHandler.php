<?php

namespace CQRSArchitecture\Handlers\CommandHandlers;

use CQRSArchitecture\Commands\DeleteUserCommand;
use CQRSArchitecture\Models\User;
use CQRSArchitecture\Models\UserReadModel;
use CQRSArchitecture\EventStore\EventStore;

/**
 * DeleteUserHandler
 * 
 * This class handles the DeleteUserCommand.
 * It deletes an existing user and stores the events.
 */
class DeleteUserHandler
{
    /**
     * Handle the command
     * 
     * @param DeleteUserCommand $command The command to handle
     * @return bool True if the user was deleted, false otherwise
     */
    public function handle(DeleteUserCommand $command): bool
    {
        // Get the user from the read model
        $userData = UserReadModel::getUserById($command->getUserId());
        
        if (!$userData) {
            return false;
        }
        
        // Create a User object from the read model data
        $user = new User(
            $userData['id'],
            $userData['name'],
            $userData['email'],
            $userData['password'] ?? '', // Password might not be in the read model
            $userData['created_at']
        );
        
        // Mark the user as deleted
        $user->delete();
        
        // Store the events
        EventStore::storeMany($user->getRecordedEvents());
        
        return true;
    }
}
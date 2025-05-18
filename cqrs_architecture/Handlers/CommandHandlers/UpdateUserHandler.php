<?php

namespace CQRSArchitecture\Handlers\CommandHandlers;

use CQRSArchitecture\Commands\UpdateUserCommand;
use CQRSArchitecture\Models\User;
use CQRSArchitecture\Models\UserReadModel;
use CQRSArchitecture\EventStore\EventStore;

/**
 * UpdateUserHandler
 * 
 * This class handles the UpdateUserCommand.
 * It updates an existing user and stores the events.
 */
class UpdateUserHandler
{
    /**
     * Handle the command
     * 
     * @param UpdateUserCommand $command The command to handle
     * @return bool True if the user was updated, false otherwise
     * @throws \InvalidArgumentException
     */
    public function handle(UpdateUserCommand $command): bool
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
        
        // Update the user
        $user->update(
            $command->hasNameUpdate() ? $command->getName() : null,
            $command->hasEmailUpdate() ? $command->getEmail() : null,
            $command->hasPasswordUpdate() ? $command->getPassword() : null
        );
        
        // Store the events
        EventStore::storeMany($user->getRecordedEvents());
        
        return true;
    }
}
<?php

namespace CQRSArchitecture\Handlers\CommandHandlers;

use CQRSArchitecture\Commands\CreateUserCommand;
use CQRSArchitecture\Models\User;
use CQRSArchitecture\Models\UserReadModel;
use CQRSArchitecture\EventStore\EventStore;

/**
 * CreateUserHandler
 * 
 * This class handles the CreateUserCommand.
 * It creates a new user and stores the events.
 */
class CreateUserHandler
{
    /**
     * Handle the command
     * 
     * @param CreateUserCommand $command The command to handle
     * @return int The ID of the created user
     * @throws \InvalidArgumentException
     */
    public function handle(CreateUserCommand $command): int
    {
        // Generate a new user ID
        $userId = UserReadModel::generateId();
        
        // Create the user
        $user = User::create(
            $userId,
            $command->getName(),
            $command->getEmail(),
            $command->getPassword()
        );
        
        // Store the events
        EventStore::storeMany($user->getRecordedEvents());
        
        return $userId;
    }
}
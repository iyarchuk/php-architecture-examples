<?php

namespace EventSourcingArchitecture\CommandHandlers;

use EventSourcingArchitecture\Commands\Command;

/**
 * CommandHandler Interface
 * 
 * This interface defines the contract for command handlers in the system.
 * Command handlers are responsible for processing commands and generating events.
 */
interface CommandHandler
{
    /**
     * Handle a command
     * 
     * @param Command $command The command to handle
     * @return mixed The result of handling the command
     * @throws \Exception If the command cannot be handled
     */
    public function handle(Command $command);
    
    /**
     * Check if this handler can handle the given command
     * 
     * @param Command $command The command to check
     * @return bool
     */
    public function canHandle(Command $command): bool;
}
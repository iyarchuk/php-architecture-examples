<?php

namespace EventSourcingArchitecture\CommandHandlers;

use EventSourcingArchitecture\Commands\Command;
use EventSourcingArchitecture\Commands\CreateAccountCommand;
use EventSourcingArchitecture\Aggregates\BankAccount;
use EventSourcingArchitecture\EventStore\EventStore;

/**
 * CreateAccountCommandHandler
 * 
 * This class handles commands to create a new bank account.
 */
class CreateAccountCommandHandler implements CommandHandler
{
    /**
     * @var EventStore The event store
     */
    private EventStore $eventStore;
    
    /**
     * Constructor
     * 
     * @param EventStore $eventStore The event store
     */
    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }
    
    /**
     * Handle a command
     * 
     * @param Command $command The command to handle
     * @return string The ID of the created account
     * @throws \InvalidArgumentException If the command is invalid
     * @throws \RuntimeException If the command cannot be handled
     */
    public function handle(Command $command)
    {
        if (!$this->canHandle($command)) {
            throw new \RuntimeException(sprintf(
                'Cannot handle command of type %s',
                get_class($command)
            ));
        }
        
        /** @var CreateAccountCommand $command */
        
        // Validate the command
        $command->validate();
        
        // Create a new bank account
        $account = BankAccount::create(
            $command->getAccountNumber(),
            $command->getAccountHolderName(),
            $command->getAccountType(),
            $command->getInitialBalance(),
            $command->getCurrency()
        );
        
        // Get the uncommitted events from the aggregate
        $events = $account->getUncommittedEvents();
        
        // Append the events to the event store
        $this->eventStore->appendToStream(
            $account->getId(),
            $events,
            0 // Expected version is 0 for a new aggregate
        );
        
        // Clear the uncommitted events from the aggregate
        $account->clearUncommittedEvents();
        
        // Return the ID of the created account
        return $account->getId();
    }
    
    /**
     * Check if this handler can handle the given command
     * 
     * @param Command $command The command to check
     * @return bool
     */
    public function canHandle(Command $command): bool
    {
        return $command instanceof CreateAccountCommand;
    }
}
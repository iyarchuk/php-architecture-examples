<?php

namespace EventSourcingArchitecture\CommandHandlers;

use EventSourcingArchitecture\Commands\Command;
use EventSourcingArchitecture\Commands\DebitAccountCommand;
use EventSourcingArchitecture\Aggregates\BankAccount;
use EventSourcingArchitecture\EventStore\EventStore;

/**
 * DebitAccountCommandHandler
 * 
 * This class handles commands to debit (withdraw) money from a bank account.
 */
class DebitAccountCommandHandler implements CommandHandler
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
     * @return bool True if the command was handled successfully
     * @throws \InvalidArgumentException If the command is invalid
     * @throws \RuntimeException If the command cannot be handled
     * @throws \DomainException If the account would be overdrawn
     */
    public function handle(Command $command)
    {
        if (!$this->canHandle($command)) {
            throw new \RuntimeException(sprintf(
                'Cannot handle command of type %s',
                get_class($command)
            ));
        }

        /** @var DebitAccountCommand $command */

        // Validate the command
        $command->validate();

        $accountId = $command->getAccountId();
        $expectedVersion = $command->getExpectedVersion();

        // Check if the account exists
        if (!$this->eventStore->aggregateExists($accountId)) {
            throw new \RuntimeException(sprintf(
                'Account with ID %s does not exist',
                $accountId
            ));
        }

        // If no expected version was provided, use the current version
        if ($expectedVersion === null) {
            $expectedVersion = $this->eventStore->getCurrentVersion($accountId);
        }

        // Get the events for the account
        $events = $this->eventStore->getEventsForAggregate($accountId);

        // Reconstruct the account from the events
        $account = BankAccount::fromEvents($accountId, $events);

        // Debit the account
        $account->debit(
            $command->getAmount(),
            $command->getDescription(),
            $command->getTransactionId() ?? ''
        );

        // Get the uncommitted events from the aggregate
        $uncommittedEvents = $account->getUncommittedEvents();

        // Append the events to the event store
        $this->eventStore->appendToStream(
            $accountId,
            $uncommittedEvents,
            $expectedVersion
        );

        // Clear the uncommitted events from the aggregate
        $account->clearUncommittedEvents();

        return true;
    }

    /**
     * Check if this handler can handle the given command
     * 
     * @param Command $command The command to check
     * @return bool
     */
    public function canHandle(Command $command): bool
    {
        return $command instanceof DebitAccountCommand;
    }
}

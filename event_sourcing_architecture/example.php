<?php

// Include autoloader (in a real application, you would use Composer's autoloader)
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $file = __DIR__ . '/' . str_replace('\\', '/', str_replace('EventSourcingArchitecture\\', '', $class)) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use EventSourcingArchitecture\EventStore\InMemoryEventStore;
use EventSourcingArchitecture\Projections\AccountBalanceProjection;
use EventSourcingArchitecture\Commands\CreateAccountCommand;
use EventSourcingArchitecture\Commands\CreditAccountCommand;
use EventSourcingArchitecture\Commands\DebitAccountCommand;
use EventSourcingArchitecture\CommandHandlers\CreateAccountCommandHandler;
use EventSourcingArchitecture\CommandHandlers\CreditAccountCommandHandler;
use EventSourcingArchitecture\CommandHandlers\DebitAccountCommandHandler;

echo "=== Event Sourcing Architecture Example ===\n";
echo "This example demonstrates the Event Sourcing architectural pattern.\n";
echo "It shows how to structure a banking system following the Event Sourcing approach.\n\n";

// Create the event store
echo "STEP 1: Creating the event store\n";
$eventStore = new InMemoryEventStore();
echo "Event store created.\n\n";

// Create the projection
echo "STEP 2: Creating the projection\n";
$projection = new AccountBalanceProjection();
echo "Projection created.\n\n";

// Create the command handlers
echo "STEP 3: Creating the command handlers\n";
$createAccountHandler = new CreateAccountCommandHandler($eventStore);
$creditAccountHandler = new CreditAccountCommandHandler($eventStore);
$debitAccountHandler = new DebitAccountCommandHandler($eventStore);
echo "Command handlers created.\n\n";

// Scenario 1: Create a new bank account
echo "SCENARIO 1: Creating a new bank account\n";
echo "----------------------------------------\n";
$createAccountCommand = new CreateAccountCommand(
    'ACC-123456',
    'John Doe',
    'checking',
    1000.00,
    'USD'
);

try {
    $accountId = $createAccountHandler->handle($createAccountCommand);
    echo "Account created with ID: $accountId\n";
    
    // Project the events to update the read model
    $events = $eventStore->getEventsForAggregate($accountId);
    foreach ($events as $event) {
        $projection->project($event);
    }
    
    // Display account details
    $accountDetails = $projection->getAccountDetails($accountId);
    echo "Account Details:\n";
    echo "- Account Number: {$accountDetails['account_number']}\n";
    echo "- Account Holder: {$accountDetails['account_holder_name']}\n";
    echo "- Account Type: {$accountDetails['account_type']}\n";
    echo "- Balance: {$accountDetails['balance']} {$accountDetails['currency']}\n";
    echo "- Created At: {$accountDetails['created_at']}\n";
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}
echo "\n";

// Scenario 2: Credit money to the account
echo "SCENARIO 2: Crediting money to the account\n";
echo "------------------------------------------\n";
$creditAccountCommand = new CreditAccountCommand(
    $accountId,
    500.00,
    'Salary deposit'
);

try {
    $creditAccountHandler->handle($creditAccountCommand);
    echo "Money credited to the account.\n";
    
    // Project the events to update the read model
    $events = $eventStore->getEventsForAggregate($accountId);
    foreach ($events as $event) {
        $projection->project($event);
    }
    
    // Display updated balance
    $balance = $projection->getBalance($accountId);
    echo "New Balance: $balance USD\n";
    
    // Display transaction history
    $transactions = $projection->getTransactionHistory($accountId);
    echo "Transaction History:\n";
    foreach ($transactions as $transaction) {
        echo "- {$transaction['timestamp']}: {$transaction['type']} of {$transaction['amount']} USD";
        echo " ({$transaction['description']})";
        echo " - Balance after: {$transaction['balance_after']} USD\n";
    }
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}
echo "\n";

// Scenario 3: Debit money from the account
echo "SCENARIO 3: Debiting money from the account\n";
echo "-----------------------------------------\n";
$debitAccountCommand = new DebitAccountCommand(
    $accountId,
    300.00,
    'Rent payment'
);

try {
    $debitAccountHandler->handle($debitAccountCommand);
    echo "Money debited from the account.\n";
    
    // Project the events to update the read model
    $events = $eventStore->getEventsForAggregate($accountId);
    foreach ($events as $event) {
        $projection->project($event);
    }
    
    // Display updated balance
    $balance = $projection->getBalance($accountId);
    echo "New Balance: $balance USD\n";
    
    // Display transaction history
    $transactions = $projection->getTransactionHistory($accountId);
    echo "Transaction History:\n";
    foreach ($transactions as $transaction) {
        echo "- {$transaction['timestamp']}: {$transaction['type']} of {$transaction['amount']} USD";
        echo " ({$transaction['description']})";
        echo " - Balance after: {$transaction['balance_after']} USD\n";
    }
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}
echo "\n";

// Scenario 4: Attempt to overdraw the account
echo "SCENARIO 4: Attempting to overdraw the account\n";
echo "--------------------------------------------\n";
$overdrawCommand = new DebitAccountCommand(
    $accountId,
    2000.00,
    'Attempt to overdraw'
);

try {
    $debitAccountHandler->handle($overdrawCommand);
    echo "Money debited from the account.\n";
} catch (\DomainException $e) {
    echo "Error (expected): {$e->getMessage()}\n";
}
echo "\n";

// Display event history
echo "STEP 4: Displaying event history\n";
$allEvents = $eventStore->getAllEvents();
echo "Event History (total " . count($allEvents) . " events):\n";
foreach ($allEvents as $event) {
    echo "- " . $event->getEventType() . " at " . $event->getOccurredAt()->format('Y-m-d H:i:s');
    echo " (Aggregate ID: " . $event->getAggregateId() . ", Version: " . $event->getAggregateVersion() . ")\n";
}
echo "\n";

echo "This example demonstrates the key aspects of the Event Sourcing pattern:\n";
echo "1. All changes to the system are stored as a sequence of events\n";
echo "2. The current state can be reconstructed by replaying the events\n";
echo "3. Events provide a complete audit trail of all changes\n";
echo "4. The system can be queried at any point in time\n";
echo "5. Business rules are enforced by the domain model\n";
echo "6. Read models (projections) provide optimized views of the data\n";
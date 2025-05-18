# Event Sourcing Architecture Example

This is a simple example of a PHP application built using the Event Sourcing architectural pattern. The example demonstrates how to structure a banking system following the Event Sourcing approach.

## What is Event Sourcing?

Event Sourcing is an architectural pattern that focuses on storing all changes to an application's state as a sequence of events rather than just the current state. These events can then be used to:

1. **Reconstruct the current state** by replaying all events from the beginning
2. **Determine the application state at any point in time** by replaying events up to that point
3. **Provide a complete audit trail** of all changes to the system
4. **Enable temporal queries** to understand how the system evolved over time

The main goals of Event Sourcing are:

1. **Auditability**: Every change to the system is recorded and can be audited
2. **Temporal Queries**: The ability to determine the state of the system at any point in time
3. **Debugging**: Easier debugging by replaying events to reproduce issues
4. **Event-Driven Architecture**: Natural integration with event-driven systems

## The Components of Event Sourcing

### Events

Events are immutable objects that represent something that has happened in the system. Events:
- Contain all the data related to what happened
- Are named in the past tense (e.g., UserCreated, AccountDebited)
- Have a timestamp indicating when the event occurred
- Are stored in an event store

### Aggregates

Aggregates are domain objects that:
- Encapsulate and protect business invariants
- Apply events to change their state
- Generate new events based on commands
- Have a unique identifier
- Are reconstructed by replaying events

### Event Store

The Event Store is responsible for:
- Storing events in the order they occurred
- Retrieving events for a specific aggregate
- Providing a way to subscribe to new events
- Ensuring events are persisted durably

### Projections (Read Models)

Projections are derived data models that:
- Are built by processing events
- Provide optimized views of the data for specific use cases
- Can be rebuilt from scratch by replaying events
- Are eventually consistent with the event store

### Commands

Commands represent the intent to change the system. Commands:
- Are named in the imperative (e.g., CreateUser, DebitAccount)
- Contain all the data needed to perform the operation
- Are validated before being processed
- Result in one or more events being generated

### Command Handlers

Command Handlers are responsible for:
- Validating commands
- Loading the appropriate aggregate
- Executing the command on the aggregate
- Saving the resulting events to the event store

## The Flow in Event Sourcing

1. A client sends a command to the system
2. The command handler validates the command
3. The command handler loads the target aggregate by replaying its events
4. The aggregate executes the command, which may generate new events
5. The new events are stored in the event store
6. Projections process the new events to update their state
7. Clients query the projections to get the current state of the system

## Project Structure

This example follows the Event Sourcing principles with the following structure:

```
event_sourcing_architecture/
├── Events/                # Event classes
│   ├── Event.php         # Base event class
│   ├── AccountCreatedEvent.php
│   ├── AccountDebitedEvent.php
│   └── AccountCreditedEvent.php
├── Aggregates/           # Aggregate classes
│   ├── Aggregate.php     # Base aggregate class
│   └── BankAccount.php   # Bank account aggregate
├── EventStore/           # Event store components
│   ├── EventStore.php    # Event store interface
│   └── InMemoryEventStore.php # In-memory implementation
├── Projections/          # Projection components
│   ├── Projection.php    # Projection interface
│   └── AccountBalanceProjection.php # Account balance projection
├── Commands/             # Command classes
│   ├── Command.php       # Base command class
│   ├── CreateAccountCommand.php
│   ├── DebitAccountCommand.php
│   └── CreditAccountCommand.php
├── CommandHandlers/      # Command handler classes
│   ├── CommandHandler.php # Base command handler
│   ├── CreateAccountHandler.php
│   ├── DebitAccountHandler.php
│   └── CreditAccountHandler.php
└── example.php           # Example script demonstrating the architecture
```

## How This Example Adheres to Event Sourcing

1. **Events**:
   - Events represent significant changes to the system (account created, debited, credited)
   - Events contain all relevant data about what happened
   - Events are immutable and stored in an event store

2. **Aggregates**:
   - The BankAccount aggregate encapsulates business rules (e.g., preventing overdrafts)
   - The aggregate's state is reconstructed by replaying events
   - The aggregate generates new events based on commands

3. **Event Store**:
   - The event store persists all events
   - Events can be retrieved for a specific aggregate
   - The event store maintains the order of events

4. **Projections**:
   - The AccountBalanceProjection provides a view of account balances
   - The projection is updated when new events occur
   - The projection can be rebuilt from scratch by replaying events

5. **Commands and Command Handlers**:
   - Commands represent the intent to change the system
   - Command handlers validate commands and load aggregates
   - Command handlers save the resulting events to the event store

## Running the Example

To run the example, execute the `example.php` script:

```bash
php example.php
```

This will demonstrate:
1. Creating a bank account
2. Crediting the account
3. Debiting the account
4. Viewing the account balance
5. Viewing the account history

## Benefits of Event Sourcing

1. **Complete Audit Trail**: Every change to the system is recorded as an event
2. **Temporal Queries**: The ability to determine the state of the system at any point in time
3. **Debugging**: Easier debugging by replaying events to reproduce issues
4. **Flexibility**: The ability to add new projections without changing the core domain model
5. **Event-Driven Architecture**: Natural integration with event-driven systems

## Challenges of Event Sourcing

1. **Eventual Consistency**: Projections may lag behind the event store
2. **Learning Curve**: Event Sourcing requires a different way of thinking about state
3. **Performance**: Reconstructing aggregates by replaying events can be slow for long event streams
4. **Schema Evolution**: Handling changes to event schemas over time
5. **Snapshot Management**: Optimizing performance by taking snapshots of aggregate state

## Conclusion

This example demonstrates how to apply Event Sourcing principles to a simple PHP application. By following these principles, you can create systems that are auditable, debuggable, and flexible.
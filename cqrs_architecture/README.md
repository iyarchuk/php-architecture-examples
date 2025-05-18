# Command Query Responsibility Segregation (CQRS) Architecture Example

This is a simple example of a PHP application built using the Command Query Responsibility Segregation (CQRS) architectural pattern. The example demonstrates how to structure a user management system following the CQRS approach.

## What is CQRS Architecture?

Command Query Responsibility Segregation (CQRS) is an architectural pattern that separates read and update operations for a data store. It uses different models for updating and reading information:

1. **Command Side**: Handles create, update, and delete operations (writes)
2. **Query Side**: Handles read operations (reads)

The main goals of CQRS Architecture are:

1. **Separation of concerns**: Different models for reading and writing data
2. **Scalability**: Read and write operations can be scaled independently
3. **Performance**: Optimized models for specific operations
4. **Flexibility**: Different data storage technologies can be used for reads and writes

## The Components of CQRS Architecture

### Commands

Commands represent the intent to change the state of the system. They:
- Are named with imperative verbs (e.g., CreateUser, UpdateUserEmail)
- Contain all the data needed to perform the operation
- Are immutable
- Do not return data

### Command Handlers

Command Handlers process commands and apply the changes to the domain model. They:
- Validate the command
- Load the appropriate domain model
- Apply the changes
- Save the updated model
- Optionally publish events

### Queries

Queries represent requests for information. They:
- Are named with interrogative phrases (e.g., GetUserById, GetAllUsers)
- Specify the criteria for the data being requested
- Do not change the state of the system

### Query Handlers

Query Handlers process queries and return the requested data. They:
- Retrieve data from the read model
- Format the data as needed
- Return the data to the caller

### Domain Models

Domain Models represent the business entities and their behavior. In CQRS, they:
- Are used by the command side
- Encapsulate business rules and validation
- May emit domain events when state changes

### Read Models

Read Models are optimized for querying. They:
- Are used by the query side
- Are denormalized for efficient querying
- Are updated based on events from the domain model
- Do not contain business logic

### Event Store

The Event Store is a database that stores the sequence of events that have occurred in the system. It:
- Provides a complete audit trail of all changes
- Allows the system to be rebuilt from scratch
- Enables event sourcing (if used)

## The Flow in CQRS Architecture

1. Client sends a command to change the system state
2. Command Handler validates the command and applies it to the Domain Model
3. Domain Model changes state and emits events
4. Events are stored in the Event Store
5. Read Models are updated based on the events
6. Client sends a query to retrieve data
7. Query Handler retrieves data from the Read Model and returns it to the client

## Project Structure

This example follows the CQRS Architecture principles with the following structure:

```
cqrs_architecture/
├── Commands/                # Command objects
│   ├── CreateUserCommand.php
│   ├── UpdateUserCommand.php
│   └── DeleteUserCommand.php
├── Handlers/               # Command and Query Handlers
│   ├── CommandHandlers/
│   │   ├── CreateUserHandler.php
│   │   ├── UpdateUserHandler.php
│   │   └── DeleteUserHandler.php
│   └── QueryHandlers/
│       ├── GetUserByIdHandler.php
│       └── GetAllUsersHandler.php
├── Queries/                # Query objects
│   ├── GetUserByIdQuery.php
│   └── GetAllUsersQuery.php
├── Models/                 # Domain and Read Models
│   ├── User.php            # Domain Model
│   └── UserReadModel.php   # Read Model
├── EventStore/             # Event Store
│   ├── Event.php
│   ├── UserCreatedEvent.php
│   ├── UserUpdatedEvent.php
│   ├── UserDeletedEvent.php
│   └── EventStore.php
└── example.php             # Example script demonstrating the architecture
```

## How This Example Adheres to CQRS Architecture

1. **Separation of Read and Write Models**:
   - The Domain Model (User.php) is used for write operations
   - The Read Model (UserReadModel.php) is used for read operations

2. **Command Pattern**:
   - Commands (CreateUserCommand, UpdateUserCommand, DeleteUserCommand) represent the intent to change the system
   - Command Handlers process these commands and apply changes to the Domain Model

3. **Query Pattern**:
   - Queries (GetUserByIdQuery, GetAllUsersQuery) represent requests for information
   - Query Handlers process these queries and return data from the Read Model

4. **Event Sourcing**:
   - Events (UserCreatedEvent, UserUpdatedEvent, UserDeletedEvent) represent state changes
   - The Event Store maintains a log of all events
   - The Read Model is updated based on these events

## Running the Example

To run the example, execute the `example.php` script:

```bash
php example.php
```

This will demonstrate:
1. Creating a user
2. Retrieving a user by ID
3. Getting all users
4. Updating a user
5. Deleting a user

## Benefits of This Architecture

1. **Scalability**: Read and write operations can be scaled independently
2. **Performance**: Models can be optimized for specific operations
3. **Flexibility**: Different data storage technologies can be used for reads and writes
4. **Maintainability**: Separation of concerns makes the code easier to understand and maintain
5. **Auditability**: Event sourcing provides a complete history of all changes

## Conclusion

This example demonstrates how to apply CQRS Architecture principles to a simple PHP application. By following these principles, you can create systems that are more scalable, performant, and maintainable.
# Hexagonal Architecture Example

This is a simple example of a PHP application built using Hexagonal Architecture principles (also known as Ports and Adapters Architecture). The example demonstrates how to structure a user management system following the Hexagonal Architecture approach.

## What is Hexagonal Architecture?

Hexagonal Architecture is a software design pattern introduced by Alistair Cockburn that aims to create loosely coupled application components that can be easily connected to their software environment by means of ports and adapters. This makes components exchangeable at any level and facilitates test automation.

The main goals of Hexagonal Architecture are:

1. **Separation of concerns**: Clearly separate the business logic from external concerns.
2. **Independence from external systems**: The core business logic doesn't depend on external systems like databases, UI, or frameworks.
3. **Testability**: Business logic can be tested in isolation without external dependencies.
4. **Flexibility**: External components can be replaced without affecting the business logic.

## The Layers of Hexagonal Architecture

Hexagonal Architecture consists of three main layers:

1. **Domain** (innermost layer): Contains the business logic and entities. This is the core of the application.
2. **Ports**: Interfaces that define how the domain interacts with the outside world. There are two types of ports:
   - **Primary/Driving Ports**: Interfaces that allow external systems to interact with the domain.
   - **Secondary/Driven Ports**: Interfaces that the domain uses to interact with external systems.
3. **Adapters** (outermost layer): Implementations of the ports that connect the domain to external systems. There are two types of adapters:
   - **Primary/Driving Adapters**: Implementations of the primary ports that drive the domain (e.g., controllers, CLI).
   - **Secondary/Driven Adapters**: Implementations of the secondary ports that are driven by the domain (e.g., repositories, external services).

## The Dependency Rule

The fundamental rule that makes this architecture work is the **Dependency Rule**:

> Dependencies always point inward, toward the domain.

The domain doesn't depend on any external systems. It defines ports (interfaces) that external systems must implement to interact with it.

## Project Structure

This example follows the Hexagonal Architecture principles with the following structure:

```
hexagonal_architecture_example/
├── Domain/                 # Domain layer (core business logic)
│   ├── User.php            # User entity
│   └── UserService.php     # Service that implements the primary port
├── Ports/                  # Ports (interfaces)
│   ├── Primary/            # Primary/Driving ports
│   │   └── UserServicePort.php  # Interface for user operations
│   └── Secondary/          # Secondary/Driven ports
│       └── UserRepositoryPort.php  # Interface for user repository
├── Adapters/               # Adapters (implementations of ports)
│   ├── Primary/            # Primary/Driving adapters
│   │   └── UserController.php  # Controller for handling user requests
│   └── Secondary/          # Secondary/Driven adapters
│       └── InMemoryUserRepository.php  # Repository implementation
└── example.php             # Example script demonstrating the architecture
```

## How This Example Adheres to Hexagonal Architecture

1. **Domain Layer**:
   - Contains the `User` entity with its business rules (e.g., email validation, password hashing).
   - Contains the `UserService` that implements the primary port and uses the secondary port.

2. **Ports**:
   - **Primary/Driving Ports**: The `UserServicePort` interface defines how external systems can interact with the domain.
   - **Secondary/Driven Ports**: The `UserRepositoryPort` interface defines how the domain interacts with external systems for data persistence.

3. **Adapters**:
   - **Primary/Driving Adapters**: The `UserController` implements the primary port and drives the domain.
   - **Secondary/Driven Adapters**: The `InMemoryUserRepository` implements the secondary port and is driven by the domain.

## Running the Example

To run the example, execute the `example.php` script:

```bash
php example.php
```

This will demonstrate:
1. Creating a user
2. Retrieving a user by ID
3. Getting all users
4. Trying to create a user with invalid data
5. Trying to create a user with missing data
6. Deleting a user
7. Trying to retrieve a deleted user

## Benefits of This Architecture

1. **Maintainability**: The code is organized in a way that makes it easy to understand and modify.
2. **Testability**: Each layer can be tested independently, with mock implementations of dependencies.
3. **Flexibility**: The implementation details (database, UI, etc.) can be changed without affecting the business rules.
4. **Independence**: The business rules are independent of the UI, database, and any external agency.

## Hexagonal vs. Clean Architecture

Hexagonal Architecture and Clean Architecture are similar in many ways, but there are some differences:

1. **Terminology**: Hexagonal Architecture uses terms like "ports" and "adapters," while Clean Architecture uses terms like "use cases" and "interface adapters."
2. **Visualization**: Hexagonal Architecture is visualized as a hexagon with ports on the edges, while Clean Architecture is visualized as concentric circles.
3. **Focus**: Hexagonal Architecture focuses on the separation of the domain from external systems, while Clean Architecture also emphasizes the separation of different layers within the application.

Despite these differences, both architectures share the same core principles of separation of concerns and dependency inversion.

## Conclusion

This example demonstrates how to apply Hexagonal Architecture principles to a simple PHP application. By following these principles, you can create systems that are more maintainable, testable, and flexible.
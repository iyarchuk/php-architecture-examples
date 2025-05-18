# Clean Architecture Example

This is a simple example of a PHP application built using Clean Architecture principles. The example demonstrates how to structure a user management system following the Clean Architecture approach.

## What is Clean Architecture?

Clean Architecture is a software design philosophy introduced by Robert C. Martin (Uncle Bob) that aims to create systems that are:

1. **Independent of frameworks**: The architecture doesn't depend on the existence of some library or framework.
2. **Testable**: Business rules can be tested without UI, database, web server, or any external element.
3. **Independent of the UI**: The UI can change easily, without changing the rest of the system.
4. **Independent of the database**: You can swap out one database for another without affecting the business rules.
5. **Independent of any external agency**: The business rules don't know anything about the outside world.

## The Layers of Clean Architecture

Clean Architecture is typically represented as concentric circles, each representing different layers of the software:

1. **Domain** (innermost circle): Contains enterprise-wide business rules and entities.
2. **Application**: Contains application-specific business rules (use cases).
3. **Infrastructure**: Contains adapters that convert data from the format most convenient for the use cases and entities, to the format most convenient for some external agency such as a database.
4. **Presentation** (outermost circle): Contains frameworks, tools, and delivery mechanisms.

## The Dependency Rule

The fundamental rule that makes this architecture work is the **Dependency Rule**:

> Source code dependencies must point only inward, toward higher-level policies.

Nothing in an inner circle can know anything at all about something in an outer circle.

## Project Structure

This example follows the Clean Architecture principles with the following structure:

```
clean_architecture_example/
├── Domain/                 # Domain layer (entities and business rules)
│   ├── User.php            # User entity
│   └── UserRepositoryInterface.php  # Repository interface
├── Application/            # Application layer (use cases)
│   ├── CreateUserUseCase.php  # Use case for creating a user
│   └── GetUserUseCase.php     # Use case for retrieving a user
├── Infrastructure/         # Infrastructure layer (adapters)
│   └── InMemoryUserRepository.php  # Repository implementation
├── Presentation/           # Presentation layer (controllers)
│   └── UserController.php  # Controller for handling user requests
└── example.php             # Example script demonstrating the architecture
```

## How This Example Adheres to Clean Architecture

1. **Domain Layer**:
   - Contains the `User` entity with its business rules (e.g., email validation, password hashing).
   - Defines the `UserRepositoryInterface` that specifies how to access user data without implementation details.

2. **Application Layer**:
   - Contains use cases that orchestrate the flow of data to and from entities.
   - Uses DTOs (Data Transfer Objects) for input and output data.
   - Depends only on the Domain layer, not on Infrastructure or Presentation.

3. **Infrastructure Layer**:
   - Implements the `UserRepositoryInterface` from the Domain layer.
   - Contains the concrete implementation of how to store and retrieve users.
   - Depends on the Domain layer, but the Domain layer doesn't depend on it.

4. **Presentation Layer**:
   - Contains controllers that handle HTTP requests and responses.
   - Uses the use cases from the Application layer to perform operations.
   - Depends on the Application layer, but the Application layer doesn't depend on it.

## Running the Example

To run the example, execute the `example.php` script:

```bash
php example.php
```

This will demonstrate:
1. Creating a user
2. Retrieving a user by ID
3. Trying to retrieve a non-existent user
4. Trying to create a user with invalid data
5. Trying to create a user with missing data

## Benefits of This Architecture

1. **Maintainability**: The code is organized in a way that makes it easy to understand and modify.
2. **Testability**: Each layer can be tested independently, with mock implementations of dependencies.
3. **Flexibility**: The implementation details (database, UI, etc.) can be changed without affecting the business rules.
4. **Independence**: The business rules are independent of the UI, database, and any external agency.

## Conclusion

This example demonstrates how to apply Clean Architecture principles to a simple PHP application. By following these principles, you can create systems that are more maintainable, testable, and flexible.
# Layered Architecture Example

This is a simple example of a PHP application built using Layered Architecture principles. The example demonstrates how to structure a user management system following the Layered Architecture approach.

## What is Layered Architecture?

Layered Architecture is one of the most common architectural patterns that organizes an application into horizontal layers, each with a specific role and responsibility. Each layer has a specific set of tasks and should only communicate with adjacent layers.

The main goals of Layered Architecture are:

1. **Separation of concerns**: Each layer has a specific responsibility.
2. **Maintainability**: Changes in one layer should not affect other layers.
3. **Testability**: Each layer can be tested independently.
4. **Reusability**: Higher layers can be reused with different lower layers.

## The Layers of Layered Architecture

A typical Layered Architecture consists of four main layers:

1. **Presentation Layer** (topmost layer): Handles user interface and user interaction. It's responsible for displaying information to the user and interpreting user commands.

2. **Business Layer**: Contains the business logic, business rules, and workflows. It processes the data between the presentation and persistence layers.

3. **Persistence Layer**: Responsible for data access logic. It provides a simplified interface to interact with the database or other data sources.

4. **Database Layer** (bottommost layer): Handles data storage and retrieval. It's typically a database management system.

## The Dependency Rule

In Layered Architecture, dependencies flow downward. Higher layers depend on lower layers, but lower layers should not depend on higher layers.

## Project Structure

This example follows the Layered Architecture principles with the following structure:

```
layered_architecture/
├── Presentation/           # Presentation layer
│   └── UserController.php  # Controller for handling user requests
├── Business/               # Business layer
│   └── UserService.php     # Service for user business logic
├── Persistence/            # Persistence layer
│   └── UserRepository.php  # Repository for user data access
├── Database/               # Database layer
│   └── DatabaseConnection.php  # Database connection handler
├── Model/                  # Shared models
│   └── User.php            # User model
└── example.php             # Example script demonstrating the architecture
```

## How This Example Adheres to Layered Architecture

1. **Presentation Layer**:
   - Contains the `UserController` that handles user requests and responses.
   - Depends on the Business Layer.

2. **Business Layer**:
   - Contains the `UserService` that implements business logic.
   - Depends on the Persistence Layer.

3. **Persistence Layer**:
   - Contains the `UserRepository` that handles data access.
   - Depends on the Database Layer.

4. **Database Layer**:
   - Contains the `DatabaseConnection` that handles database operations.
   - Doesn't depend on any other layer.

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

1. **Maintainability**: The code is organized in a way that makes it easy to understand and modify.
2. **Testability**: Each layer can be tested independently.
3. **Flexibility**: The implementation details of each layer can be changed without affecting other layers.
4. **Scalability**: Different layers can be scaled independently based on the needs.

## Conclusion

This example demonstrates how to apply Layered Architecture principles to a simple PHP application. By following these principles, you can create systems that are more maintainable, testable, and flexible.
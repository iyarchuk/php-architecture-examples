# Onion Architecture

## Overview

Onion Architecture is a software architectural pattern that puts the domain model and business logic at the center of the application, with infrastructure and UI as external layers. It's similar to Clean Architecture but with more emphasis on the domain model.

The key principle of Onion Architecture is that dependencies always point inward. Inner layers don't depend on outer layers, which makes the core business logic independent of implementation details like databases, frameworks, or UI.

## Layers

Onion Architecture consists of concentric layers:

1. **Domain Layer (Core)**: The innermost layer containing entities, value objects, domain services, and repository interfaces. This layer contains the core business logic and rules.

2. **Application Layer**: Contains application services, use cases, and DTOs (Data Transfer Objects). This layer coordinates the flow of data to and from the Domain layer.

3. **Infrastructure Layer**: Contains implementations of the interfaces defined in the Domain layer, such as repositories, external services, etc.

4. **Presentation Layer**: Contains UI components, controllers, etc. This is the outermost layer that interacts with the user.

## Key Principles

1. **Dependency Rule**: Dependencies always point inward. Inner layers don't depend on outer layers.
2. **Domain-Centric**: The domain model is at the center of the architecture.
3. **Separation of Concerns**: Each layer has a specific responsibility.
4. **Testability**: The architecture makes it easy to test the application, especially the core business logic.

## Example Implementation

This example demonstrates a simple user management system using Onion Architecture:

### Domain Layer

- `User.php`: The User entity with core business logic
- `UserRepository.php`: Interface for user persistence
- `UserService.php`: Domain service with business logic

### Application Layer

- `UserApplicationService.php`: Application service that coordinates the flow of data
- `CreateUserRequest.php`: DTO for creating a user
- `GetUserRequest.php`: DTO for retrieving a user

### Infrastructure Layer

- `InMemoryUserRepository.php`: In-memory implementation of the UserRepository interface

### Presentation Layer

- `UserController.php`: Controller that handles HTTP requests and responses

## How to Run the Example

The `example.php` file demonstrates how to use the Onion Architecture pattern. It shows:

1. Creating dependencies from inside out (Infrastructure -> Domain -> Application -> Presentation)
2. Creating a user
3. Retrieving a user
4. Handling error cases

To run the example:

```bash
php example.php
```

## Benefits of Onion Architecture

1. **Maintainability**: The architecture makes it easy to maintain and evolve the application over time.
2. **Testability**: The core business logic can be tested independently of infrastructure concerns.
3. **Flexibility**: The architecture allows for easy replacement of components, such as switching from one database to another.
4. **Focus on Domain**: The architecture puts the focus on the domain model and business logic, which is the most important part of the application.
# Domain-Driven Design (DDD) Architecture Example

This is a simple example of a PHP application built using the Domain-Driven Design (DDD) architectural pattern. The example demonstrates how to structure a user management system following the DDD approach.

## What is Domain-Driven Design?

Domain-Driven Design (DDD) is an approach to software development that centers the development on programming a domain model with a rich understanding of the processes and rules of a domain. The term was coined by Eric Evans in his book "Domain-Driven Design: Tackling Complexity in the Heart of Software" (2003).

The main goals of Domain-Driven Design are:

1. **Focus on the Core Domain**: Concentrate efforts on the core business domain and domain logic.
2. **Base Complex Designs on a Model**: Base complex designs on a model of the domain.
3. **Collaboration**: Engage in creative collaboration between technical and domain experts to iteratively refine a conceptual model that addresses domain problems.
4. **Ubiquitous Language**: Develop a shared language between developers and domain experts.

## The Components of Domain-Driven Design

### Domain Layer

The Domain Layer is the heart of the software, and it's where the concepts of the business domain and business rules are expressed:

1. **Entities**: Objects that have a distinct identity that runs through time and different states. They are defined by their identity, rather than their attributes.
2. **Value Objects**: Objects that have no conceptual identity, and are defined by their attributes.
3. **Aggregates**: Clusters of associated objects that are treated as a unit for data changes. Each aggregate has a root and a boundary.
4. **Domain Events**: Objects that define an event (something that happened) in the domain.
5. **Domain Services**: Operations that don't naturally fit within an entity or value object.
6. **Repositories**: Methods for retrieving domain objects from a database.
7. **Factories**: Methods for creating domain objects.

### Application Layer

The Application Layer is responsible for coordinating the application activity:

1. **Application Services**: Orchestrate the execution of domain logic and manage the transaction boundaries.
2. **Data Transfer Objects (DTOs)**: Objects that carry data between processes.

### Infrastructure Layer

The Infrastructure Layer provides generic technical capabilities that support the higher layers:

1. **Repositories Implementation**: Concrete implementations of domain repositories.
2. **Persistence**: Database access, ORM configurations, etc.
3. **Messaging**: Implementation of message queues, event buses, etc.
4. **External Interfaces**: Implementation of interfaces to external systems.

### Presentation Layer

The Presentation Layer is responsible for showing information to the user and interpreting the user's commands:

1. **Controllers**: Handle user input and convert it to commands for the application.
2. **Views**: Display information to the user.

## The Flow in Domain-Driven Design

1. User interacts with the Presentation Layer
2. Presentation Layer sends commands to the Application Layer
3. Application Layer uses Domain Layer to execute business logic
4. Domain Layer may use Infrastructure Layer for persistence
5. Results flow back through the layers to the user

## Project Structure

This example follows the Domain-Driven Design principles with the following structure:

```
domain_driven_design/
├── Domain/                 # Domain Layer
│   ├── Entities/           # Domain entities
│   ├── ValueObjects/       # Value objects
│   ├── Services/           # Domain services
│   └── Aggregates/         # Aggregates and aggregate roots
├── Application/            # Application Layer
│   ├── Services/           # Application services
│   └── DTOs/               # Data Transfer Objects
├── Infrastructure/         # Infrastructure Layer
│   ├── Repositories/       # Repository implementations
│   └── Persistence/        # Persistence implementations
├── Presentation/           # Presentation Layer
│   ├── Controllers/        # Controllers
│   └── Views/              # Views
└── example.php             # Example script demonstrating the architecture
```

## How This Example Adheres to Domain-Driven Design

1. **Domain Layer**:
   - Contains entities, value objects, and domain services
   - Encapsulates the core business logic
   - Uses a ubiquitous language shared with domain experts

2. **Application Layer**:
   - Orchestrates the execution of domain logic
   - Manages transaction boundaries
   - Provides a facade for the domain layer

3. **Infrastructure Layer**:
   - Provides implementations for repositories
   - Handles persistence concerns
   - Isolates the domain from external concerns

4. **Presentation Layer**:
   - Handles user input and output
   - Converts user commands to application service calls
   - Displays information to the user

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

## Benefits of Domain-Driven Design

1. **Alignment with Business**: The software model aligns closely with the business model.
2. **Maintainability**: The codebase is organized around business concepts, making it easier to understand and maintain.
3. **Flexibility**: The domain model can evolve as the understanding of the domain evolves.
4. **Testability**: The separation of concerns makes the code easier to test.
5. **Scalability**: The architecture can scale as the application grows.

## Strategic Design Patterns in DDD

1. **Bounded Context**: A boundary within which a particular domain model is defined and applicable.
2. **Context Map**: A document that outlines the relationships between bounded contexts.
3. **Shared Kernel**: A subset of the domain model that is shared between two bounded contexts.
4. **Customer-Supplier**: A relationship between two bounded contexts where one context (supplier) provides services to another (customer).
5. **Conformist**: A relationship where one bounded context conforms to the model of another.
6. **Anticorruption Layer**: A layer that translates between two bounded contexts with different models.
7. **Open Host Service**: A protocol or interface that gives access to a bounded context as a set of services.
8. **Published Language**: A well-documented shared language that can be used to communicate between bounded contexts.

## Tactical Design Patterns in DDD

1. **Entity**: An object defined by its identity rather than its attributes.
2. **Value Object**: An object that has no conceptual identity, defined by its attributes.
3. **Aggregate**: A cluster of associated objects treated as a unit for data changes.
4. **Repository**: A mechanism for encapsulating storage, retrieval, and search behavior.
5. **Factory**: A mechanism for encapsulating complex creation logic.
6. **Service**: An operation that doesn't naturally belong to an entity or value object.
7. **Domain Event**: An object that defines an event in the domain.
8. **Module**: A grouping of related domain concepts.

## Conclusion

This example demonstrates how to apply Domain-Driven Design principles to a simple PHP application. By following these principles, you can create systems that are more maintainable, flexible, and aligned with business needs.
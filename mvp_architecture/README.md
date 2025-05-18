# Model-View-Presenter (MVP) Architecture Example

This is a simple example of a PHP application built using the Model-View-Presenter (MVP) architectural pattern. The example demonstrates how to structure a user management system following the MVP approach.

## What is MVP Architecture?

Model-View-Presenter (MVP) is a derivation of the MVC pattern that focuses on improving presentation logic:

1. **Model**: Represents the data and business logic of the application.
2. **View**: Displays the data to the user and handles user interface elements.
3. **Presenter**: Acts as a mediator between the Model and View, handling the presentation logic.

The main goals of MVP Architecture are:

1. **Separation of concerns**: Each component has a specific responsibility.
2. **Testability**: The Presenter can be tested independently of the View.
3. **Passive View**: The View is as passive as possible, with the Presenter handling most of the logic.
4. **Reusability**: Components can be reused in different parts of the application.

## The Components of MVP Architecture

### Model

The Model represents the data and business logic of the application. It:
- Manages the data, logic, and rules of the application
- Is independent of the user interface
- Directly manages the data, logic, and rules of the application

### View

The View is responsible for presenting data to the user. It:
- Renders the data provided by the Presenter
- Forwards user input to the Presenter
- Is passive and contains minimal logic
- Implements an interface that the Presenter can use

### Presenter

The Presenter acts as a mediator between the Model and View. It:
- Retrieves data from the Model
- Formats the data for display in the View
- Responds to user actions from the View
- Updates the Model as needed
- Contains the presentation logic

## The Flow in MVP Architecture

1. User interacts with the View
2. View forwards the action to the Presenter
3. Presenter updates the Model based on the action
4. Presenter retrieves data from the Model
5. Presenter formats the data and updates the View
6. View displays the formatted data to the user

## Project Structure

This example follows the MVP Architecture principles with the following structure:

```
mvp_architecture/
├── Models/                # Model layer
│   └── UserModel.php     # User model for data and business logic
├── Views/                 # View layer
│   ├── IUserView.php     # Interface for the user view
│   └── UserView.php      # Implementation of the user view
├── Presenters/           # Presenter layer
│   └── UserPresenter.php # Presenter for handling user operations
└── example.php           # Example script demonstrating the architecture
```

## How This Example Adheres to MVP Architecture

1. **Model**:
   - Contains the `UserModel` that manages user data and business logic
   - Is independent of the user interface
   - Does not interact directly with the View

2. **View**:
   - Contains the `UserView` that implements the `IUserView` interface
   - Is passive and contains minimal logic
   - Forwards user actions to the Presenter
   - Displays data formatted by the Presenter

3. **Presenter**:
   - Contains the `UserPresenter` that mediates between Model and View
   - Retrieves data from the Model and formats it for the View
   - Handles user actions from the View and updates the Model
   - Contains the presentation logic

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

1. **Testability**: The Presenter can be tested independently of the View.
2. **Separation of Concerns**: Each component has a specific responsibility.
3. **Passive View**: The View is as passive as possible, with the Presenter handling most of the logic.
4. **Reusability**: Components can be reused in different parts of the application.

## Conclusion

This example demonstrates how to apply MVP Architecture principles to a simple PHP application. By following these principles, you can create systems that are more maintainable, testable, and flexible.
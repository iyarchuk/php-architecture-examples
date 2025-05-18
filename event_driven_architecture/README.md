# Event-Driven Architecture Example

This is a simple example of a PHP application built using the Event-Driven Architecture (EDA) pattern. The example demonstrates how to structure a notification system following the EDA approach.

## What is Event-Driven Architecture?

Event-Driven Architecture (EDA) is an architectural pattern that promotes the production, detection, consumption of, and reaction to events. An event is a significant change in state or an important occurrence that needs to be communicated to other parts of the system.

The main goals of Event-Driven Architecture are:

1. **Loose Coupling**: Components are decoupled from each other, communicating only through events.
2. **Scalability**: Components can be scaled independently based on their event processing needs.
3. **Flexibility**: New components can be added without modifying existing ones.
4. **Responsiveness**: The system can react to events in real-time.

## The Components of Event-Driven Architecture

### Events

Events are the core of an event-driven architecture. They represent something that has happened in the system. Events:
- Are immutable
- Contain all the data related to what happened
- Have a type or name that identifies what kind of event it is
- Have a timestamp indicating when the event occurred

### Publishers (Producers)

Publishers are components that detect or create events and publish them to the event bus. Publishers:
- Monitor for state changes or specific conditions
- Create event objects when those conditions are met
- Publish events to the event bus
- Don't know or care which components will consume the events

### Subscribers (Consumers)

Subscribers are components that express interest in certain types of events and react when those events occur. Subscribers:
- Register their interest in specific event types
- Receive notifications when relevant events occur
- Process the events according to their business logic
- Don't know or care which components published the events

### Event Bus (Broker)

The event bus is the central component that receives events from publishers and routes them to the appropriate subscribers. The event bus:
- Maintains a registry of subscribers and their interests
- Receives events from publishers
- Determines which subscribers should receive each event
- Delivers events to the appropriate subscribers

### Event Handlers

Event handlers are the specific implementations within subscribers that process events. Event handlers:
- Contain the business logic for processing specific types of events
- Extract relevant data from events
- Perform actions based on the event data
- May produce new events as a result of processing

## The Flow in Event-Driven Architecture

1. A publisher detects a state change or condition that warrants an event
2. The publisher creates an event object with relevant data
3. The publisher sends the event to the event bus
4. The event bus determines which subscribers are interested in the event
5. The event bus delivers the event to the interested subscribers
6. Each subscriber's event handler processes the event
7. Event handlers may produce new events, continuing the cycle

## Project Structure

This example follows the Event-Driven Architecture principles with the following structure:

```
event_driven_architecture/
├── Events/                # Event classes
│   ├── Event.php         # Base event class
│   ├── UserCreatedEvent.php
│   ├── UserUpdatedEvent.php
│   └── UserDeletedEvent.php
├── Publishers/            # Publisher components
│   ├── Publisher.php     # Publisher interface
│   └── UserPublisher.php # User-related event publisher
├── Subscribers/           # Subscriber components
│   ├── Subscriber.php    # Subscriber interface
│   ├── EmailNotifier.php # Email notification subscriber
│   ├── Logger.php        # Logging subscriber
│   └── Analytics.php     # Analytics subscriber
├── EventBus/              # Event bus components
│   └── EventBus.php      # Event bus implementation
├── Handlers/              # Event handler components
│   ├── EmailNotificationHandler.php
│   ├── LoggingHandler.php
│   └── AnalyticsHandler.php
└── example.php            # Example script demonstrating the architecture
```

## How This Example Adheres to Event-Driven Architecture

1. **Events**:
   - Events represent significant occurrences in the system (user created, updated, deleted)
   - Events contain all relevant data about what happened
   - Events are immutable

2. **Publishers**:
   - Publishers detect when events occur
   - Publishers create event objects
   - Publishers send events to the event bus

3. **Subscribers**:
   - Subscribers register interest in specific event types
   - Subscribers receive events from the event bus
   - Subscribers process events using event handlers

4. **Event Bus**:
   - The event bus receives events from publishers
   - The event bus determines which subscribers should receive each event
   - The event bus delivers events to the appropriate subscribers

5. **Event Handlers**:
   - Event handlers contain the business logic for processing events
   - Event handlers extract data from events
   - Event handlers perform actions based on the event data

## Running the Example

To run the example, execute the `example.php` script:

```bash
php example.php
```

This will demonstrate:
1. Creating a user (which publishes a UserCreatedEvent)
2. Updating a user (which publishes a UserUpdatedEvent)
3. Deleting a user (which publishes a UserDeletedEvent)
4. Showing how different subscribers react to these events

## Benefits of Event-Driven Architecture

1. **Loose Coupling**: Components are decoupled, making the system more maintainable.
2. **Scalability**: Components can be scaled independently based on their event processing needs.
3. **Flexibility**: New components can be added without modifying existing ones.
4. **Responsiveness**: The system can react to events in real-time.
5. **Resilience**: Failure in one component doesn't necessarily affect others.

## Use Cases for Event-Driven Architecture

Event-Driven Architecture is particularly useful for:
1. **Microservices Communication**: Enabling loose coupling between microservices.
2. **Real-time Analytics**: Processing data as events occur.
3. **Monitoring and Alerting**: Detecting and responding to important conditions.
4. **Workflow Automation**: Triggering processes based on events.
5. **IoT Applications**: Processing data from IoT devices.

## Conclusion

This example demonstrates how to apply Event-Driven Architecture principles to a simple PHP application. By following these principles, you can create systems that are loosely coupled, scalable, flexible, and responsive.
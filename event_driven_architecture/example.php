<?php

// Include autoloader (in a real application, you would use Composer's autoloader)
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $file = __DIR__ . '/' . str_replace('\\', '/', str_replace('EventDrivenArchitecture\\', '', $class)) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use EventDrivenArchitecture\EventBus\EventBus;
use EventDrivenArchitecture\Publishers\UserPublisher;
use EventDrivenArchitecture\Subscribers\EmailNotifier;
use EventDrivenArchitecture\Subscribers\Logger;
use EventDrivenArchitecture\Subscribers\Analytics;
use EventDrivenArchitecture\Handlers\EmailNotificationHandler;
use EventDrivenArchitecture\Handlers\LoggingHandler;
use EventDrivenArchitecture\Handlers\AnalyticsHandler;

echo "=== Event-Driven Architecture Example ===\n";
echo "This example demonstrates the Event-Driven Architecture pattern.\n";
echo "It shows how to structure a notification system following the EDA approach.\n\n";

// Create the event bus
echo "STEP 1: Creating the event bus\n";
$eventBus = new EventBus();
echo "Event bus created.\n\n";

// Create the handlers
echo "STEP 2: Creating the handlers\n";
$emailHandler = new EmailNotificationHandler(true); // Simulate sending emails
$loggingHandler = new LoggingHandler('info');
$analyticsHandler = new AnalyticsHandler();
echo "Handlers created.\n\n";

// Create the subscribers
echo "STEP 3: Creating the subscribers\n";
$emailNotifier = new EmailNotifier($emailHandler);
$logger = new Logger($loggingHandler);
$analytics = new Analytics($analyticsHandler);
echo "Subscribers created.\n\n";

// Register the subscribers with the event bus
echo "STEP 4: Registering subscribers with the event bus\n";
$eventBus->register($emailNotifier);
$eventBus->register($logger);
$eventBus->register($analytics);
echo "Subscribers registered.\n\n";

// Create the publishers
echo "STEP 5: Creating the publishers\n";
$userPublisher = new UserPublisher();
$userPublisher->setEventBus($eventBus);
echo "Publishers created.\n\n";

// Demonstrate the event-driven architecture
echo "STEP 6: Demonstrating the event-driven architecture\n\n";

// Scenario 1: User Creation
echo "Scenario 1: User Creation\n";
echo "------------------------\n";
$userPublisher->publishUserCreated(1, 'John Doe', 'john.doe@example.com');
echo "\n";

// Scenario 2: User Update
echo "Scenario 2: User Update\n";
echo "----------------------\n";
$userPublisher->publishUserUpdated(1, 'John Doe', 'john.updated@example.com', ['email']);
echo "\n";

// Scenario 3: User Deletion
echo "Scenario 3: User Deletion\n";
echo "------------------------\n";
$userPublisher->publishUserDeleted(1, 'John Doe', 'john.updated@example.com', 'Account no longer needed');
echo "\n";

// Display event history
echo "STEP 7: Displaying event history\n";
$eventHistory = $eventBus->getEventHistory();
echo "Event history contains " . count($eventHistory) . " events:\n";
foreach ($eventHistory as $index => $entry) {
    $event = $entry['event'];
    $timestamp = $entry['timestamp']->format('Y-m-d H:i:s');
    echo ($index + 1) . ". [$timestamp] " . $event->getName() . "\n";
}
echo "\n";

// Display analytics summary
echo "STEP 8: Displaying analytics summary\n";
$summary = $analyticsHandler->getAnalyticsSummary();
echo "Analytics Summary:\n";
echo "- Total Registrations: " . $summary['total_registrations'] . "\n";
echo "- Total Updates: " . $summary['total_updates'] . "\n";
echo "- Total Deletions: " . $summary['total_deletions'] . "\n";
echo "\n";

echo "This example demonstrates the key aspects of the Event-Driven Architecture pattern:\n";
echo "1. Events represent significant occurrences in the system\n";
echo "2. Publishers detect events and publish them to the event bus\n";
echo "3. The event bus routes events to interested subscribers\n";
echo "4. Subscribers process events using event handlers\n";
echo "5. Components are loosely coupled, communicating only through events\n";
echo "\nThe flow of the application follows the Event-Driven Architecture pattern:\n";
echo "1. A publisher detects a state change or condition that warrants an event\n";
echo "2. The publisher creates an event object with relevant data\n";
echo "3. The publisher sends the event to the event bus\n";
echo "4. The event bus determines which subscribers are interested in the event\n";
echo "5. The event bus delivers the event to the interested subscribers\n";
echo "6. Each subscriber's event handler processes the event\n";
echo "7. Event handlers may produce new events, continuing the cycle\n";
<?php

namespace SpaceBasedArchitecture\Space;

/**
 * Interface for the Space (Tuple Space) in a Space-Based Architecture.
 * The Space is a distributed, in-memory data structure that serves as the
 * communication medium between processing units.
 */
interface SpaceInterface
{
    /**
     * Write a tuple to the space.
     *
     * @param mixed $tuple The tuple to write
     * @param string|null $id Optional ID for the tuple
     * @return string The ID of the written tuple
     */
    public function write($tuple, ?string $id = null): string;

    /**
     * Read a tuple from the space without removing it.
     * If a template is provided, it returns a tuple that matches the template.
     *
     * @param mixed|null $template Optional template to match
     * @param string|null $id Optional ID of the tuple to read
     * @return mixed|null The tuple or null if not found
     */
    public function read($template = null, ?string $id = null);

    /**
     * Take a tuple from the space (read and remove).
     * If a template is provided, it returns and removes a tuple that matches the template.
     *
     * @param mixed|null $template Optional template to match
     * @param string|null $id Optional ID of the tuple to take
     * @return mixed|null The tuple or null if not found
     */
    public function take($template = null, ?string $id = null);

    /**
     * Check if a tuple exists in the space.
     * If a template is provided, it checks if a tuple that matches the template exists.
     *
     * @param mixed|null $template Optional template to match
     * @param string|null $id Optional ID of the tuple to check
     * @return bool True if the tuple exists, false otherwise
     */
    public function exists($template = null, ?string $id = null): bool;

    /**
     * Get all tuples from the space without removing them.
     *
     * @param mixed|null $template Optional template to match
     * @return array An array of tuples
     */
    public function readAll($template = null): array;

    /**
     * Take all tuples from the space (read and remove).
     *
     * @param mixed|null $template Optional template to match
     * @return array An array of tuples
     */
    public function takeAll($template = null): array;

    /**
     * Register a listener for events in the space.
     *
     * @param string $event The event to listen for (e.g., 'write', 'take')
     * @param callable $callback The callback to execute when the event occurs
     * @return void
     */
    public function on(string $event, callable $callback): void;

    /**
     * Get the number of tuples in the space.
     *
     * @param mixed|null $template Optional template to match
     * @return int The number of tuples
     */
    public function count($template = null): int;
}
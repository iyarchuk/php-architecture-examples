<?php

namespace SpaceBasedArchitecture\Space;

/**
 * Implementation of the Space (Tuple Space) in a Space-Based Architecture.
 * This is a simplified in-memory implementation for demonstration purposes.
 * In a real-world scenario, this would be a distributed, fault-tolerant system.
 */
class Space implements SpaceInterface
{
    /**
     * The tuples stored in the space.
     *
     * @var array
     */
    private $tuples = [];

    /**
     * Event listeners.
     *
     * @var array
     */
    private $listeners = [];

    /**
     * Write a tuple to the space.
     *
     * @param mixed $tuple The tuple to write
     * @param string|null $id Optional ID for the tuple
     * @return string The ID of the written tuple
     */
    public function write($tuple, ?string $id = null): string
    {
        $id = $id ?? uniqid('tuple_');
        $this->tuples[$id] = $tuple;
        
        $this->triggerEvent('write', ['id' => $id, 'tuple' => $tuple]);
        
        return $id;
    }

    /**
     * Read a tuple from the space without removing it.
     * If a template is provided, it returns a tuple that matches the template.
     *
     * @param mixed|null $template Optional template to match
     * @param string|null $id Optional ID of the tuple to read
     * @return mixed|null The tuple or null if not found
     */
    public function read($template = null, ?string $id = null)
    {
        if ($id !== null) {
            return $this->tuples[$id] ?? null;
        }
        
        if ($template === null) {
            return !empty($this->tuples) ? reset($this->tuples) : null;
        }
        
        foreach ($this->tuples as $tupleId => $tuple) {
            if ($this->matches($tuple, $template)) {
                return $tuple;
            }
        }
        
        return null;
    }

    /**
     * Take a tuple from the space (read and remove).
     * If a template is provided, it returns and removes a tuple that matches the template.
     *
     * @param mixed|null $template Optional template to match
     * @param string|null $id Optional ID of the tuple to take
     * @return mixed|null The tuple or null if not found
     */
    public function take($template = null, ?string $id = null)
    {
        if ($id !== null) {
            $tuple = $this->tuples[$id] ?? null;
            if ($tuple !== null) {
                unset($this->tuples[$id]);
                $this->triggerEvent('take', ['id' => $id, 'tuple' => $tuple]);
                return $tuple;
            }
            return null;
        }
        
        if ($template === null) {
            if (empty($this->tuples)) {
                return null;
            }
            $id = array_key_first($this->tuples);
            $tuple = $this->tuples[$id];
            unset($this->tuples[$id]);
            $this->triggerEvent('take', ['id' => $id, 'tuple' => $tuple]);
            return $tuple;
        }
        
        foreach ($this->tuples as $tupleId => $tuple) {
            if ($this->matches($tuple, $template)) {
                unset($this->tuples[$tupleId]);
                $this->triggerEvent('take', ['id' => $tupleId, 'tuple' => $tuple]);
                return $tuple;
            }
        }
        
        return null;
    }

    /**
     * Check if a tuple exists in the space.
     * If a template is provided, it checks if a tuple that matches the template exists.
     *
     * @param mixed|null $template Optional template to match
     * @param string|null $id Optional ID of the tuple to check
     * @return bool True if the tuple exists, false otherwise
     */
    public function exists($template = null, ?string $id = null): bool
    {
        if ($id !== null) {
            return isset($this->tuples[$id]);
        }
        
        if ($template === null) {
            return !empty($this->tuples);
        }
        
        foreach ($this->tuples as $tuple) {
            if ($this->matches($tuple, $template)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get all tuples from the space without removing them.
     *
     * @param mixed|null $template Optional template to match
     * @return array An array of tuples
     */
    public function readAll($template = null): array
    {
        if ($template === null) {
            return $this->tuples;
        }
        
        $result = [];
        foreach ($this->tuples as $id => $tuple) {
            if ($this->matches($tuple, $template)) {
                $result[$id] = $tuple;
            }
        }
        
        return $result;
    }

    /**
     * Take all tuples from the space (read and remove).
     *
     * @param mixed|null $template Optional template to match
     * @return array An array of tuples
     */
    public function takeAll($template = null): array
    {
        $result = $this->readAll($template);
        
        foreach ($result as $id => $tuple) {
            unset($this->tuples[$id]);
            $this->triggerEvent('take', ['id' => $id, 'tuple' => $tuple]);
        }
        
        return $result;
    }

    /**
     * Register a listener for events in the space.
     *
     * @param string $event The event to listen for (e.g., 'write', 'take')
     * @param callable $callback The callback to execute when the event occurs
     * @return void
     */
    public function on(string $event, callable $callback): void
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }
        
        $this->listeners[$event][] = $callback;
    }

    /**
     * Get the number of tuples in the space.
     *
     * @param mixed|null $template Optional template to match
     * @return int The number of tuples
     */
    public function count($template = null): int
    {
        if ($template === null) {
            return count($this->tuples);
        }
        
        $count = 0;
        foreach ($this->tuples as $tuple) {
            if ($this->matches($tuple, $template)) {
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * Check if a tuple matches a template.
     *
     * @param mixed $tuple The tuple to check
     * @param mixed $template The template to match against
     * @return bool True if the tuple matches the template, false otherwise
     */
    private function matches($tuple, $template): bool
    {
        // Simple matching logic for demonstration purposes
        // In a real-world scenario, this would be more sophisticated
        
        // If the template is an array, check if all template keys exist in the tuple with the same values
        if (is_array($template) && is_array($tuple)) {
            foreach ($template as $key => $value) {
                if (!isset($tuple[$key]) || $tuple[$key] !== $value) {
                    return false;
                }
            }
            return true;
        }
        
        // If the template is an object, check if all template properties exist in the tuple with the same values
        if (is_object($template) && is_object($tuple)) {
            foreach (get_object_vars($template) as $key => $value) {
                if (!isset($tuple->$key) || $tuple->$key !== $value) {
                    return false;
                }
            }
            return true;
        }
        
        // For simple types, check for equality
        return $tuple === $template;
    }

    /**
     * Trigger an event.
     *
     * @param string $event The event to trigger
     * @param array $data The data to pass to the listeners
     * @return void
     */
    private function triggerEvent(string $event, array $data): void
    {
        if (!isset($this->listeners[$event])) {
            return;
        }
        
        foreach ($this->listeners[$event] as $callback) {
            call_user_func($callback, $data);
        }
    }
}
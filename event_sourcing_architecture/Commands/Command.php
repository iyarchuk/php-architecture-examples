<?php

namespace EventSourcingArchitecture\Commands;

/**
 * Command Interface
 * 
 * This interface defines the contract for commands in the system.
 * Commands represent the intent to change the state of the system.
 */
interface Command
{
    /**
     * Get the name of the command
     * 
     * @return string
     */
    public function getCommandName(): string;
    
    /**
     * Get the ID of the aggregate that this command targets
     * 
     * @return string|null
     */
    public function getAggregateId(): ?string;
    
    /**
     * Get the expected version of the aggregate
     * 
     * @return int|null
     */
    public function getExpectedVersion(): ?int;
    
    /**
     * Get the command data
     * 
     * @return array
     */
    public function getData(): array;
    
    /**
     * Validate the command
     * 
     * @throws \InvalidArgumentException If the command is invalid
     * @return void
     */
    public function validate(): void;
}
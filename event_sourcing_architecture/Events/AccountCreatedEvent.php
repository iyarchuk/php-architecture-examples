<?php

namespace EventSourcingArchitecture\Events;

/**
 * AccountCreatedEvent
 * 
 * This class represents an event that occurs when a new bank account is created.
 */
class AccountCreatedEvent extends Event
{
    /**
     * Constructor
     * 
     * @param string $aggregateId The ID of the bank account
     * @param int $aggregateVersion The version of the aggregate after this event is applied
     * @param string $accountNumber The account number
     * @param string $accountHolderName The name of the account holder
     * @param string $accountType The type of account (e.g., "checking", "savings")
     * @param float $initialBalance The initial balance of the account
     * @param string $currency The currency of the account (e.g., "USD", "EUR")
     */
    public function __construct(
        string $aggregateId,
        int $aggregateVersion,
        string $accountNumber,
        string $accountHolderName,
        string $accountType,
        float $initialBalance,
        string $currency = 'USD'
    ) {
        $data = [
            'account_number' => $accountNumber,
            'account_holder_name' => $accountHolderName,
            'account_type' => $accountType,
            'initial_balance' => $initialBalance,
            'currency' => $currency
        ];
        
        parent::__construct($aggregateId, $aggregateVersion, $data);
    }
    
    /**
     * Get the account number
     * 
     * @return string
     */
    public function getAccountNumber(): string
    {
        return $this->getData()['account_number'];
    }
    
    /**
     * Get the account holder name
     * 
     * @return string
     */
    public function getAccountHolderName(): string
    {
        return $this->getData()['account_holder_name'];
    }
    
    /**
     * Get the account type
     * 
     * @return string
     */
    public function getAccountType(): string
    {
        return $this->getData()['account_type'];
    }
    
    /**
     * Get the initial balance
     * 
     * @return float
     */
    public function getInitialBalance(): float
    {
        return $this->getData()['initial_balance'];
    }
    
    /**
     * Get the currency
     * 
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->getData()['currency'];
    }
}
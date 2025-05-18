<?php

namespace EventSourcingArchitecture\Aggregates;

use EventSourcingArchitecture\Events\Event;
use EventSourcingArchitecture\Events\AccountCreatedEvent;
use EventSourcingArchitecture\Events\AccountDebitedEvent;
use EventSourcingArchitecture\Events\AccountCreditedEvent;

/**
 * BankAccount
 * 
 * This class represents a bank account aggregate.
 * It encapsulates the business rules and state for a bank account.
 */
class BankAccount extends Aggregate
{
    /**
     * @var string The account number
     */
    private string $accountNumber;
    
    /**
     * @var string The name of the account holder
     */
    private string $accountHolderName;
    
    /**
     * @var string The type of account (e.g., "checking", "savings")
     */
    private string $accountType;
    
    /**
     * @var float The current balance of the account
     */
    private float $balance;
    
    /**
     * @var string The currency of the account (e.g., "USD", "EUR")
     */
    private string $currency;
    
    /**
     * Create a new bank account
     * 
     * @param string $accountNumber The account number
     * @param string $accountHolderName The name of the account holder
     * @param string $accountType The type of account (e.g., "checking", "savings")
     * @param float $initialBalance The initial balance of the account
     * @param string $currency The currency of the account (e.g., "USD", "EUR")
     * @return static
     */
    public static function create(
        string $accountNumber,
        string $accountHolderName,
        string $accountType,
        float $initialBalance = 0.0,
        string $currency = 'USD'
    ): self {
        $id = self::generateId();
        $account = new self($id);
        
        $event = new AccountCreatedEvent(
            $id,
            1, // First event, so version is 1
            $accountNumber,
            $accountHolderName,
            $accountType,
            $initialBalance,
            $currency
        );
        
        $account->apply($event);
        
        return $account;
    }
    
    /**
     * Debit (withdraw) money from the account
     * 
     * @param float $amount The amount to debit
     * @param string $description The description of the transaction
     * @param string $transactionId The ID of the transaction
     * @throws \InvalidArgumentException If the amount is negative or zero
     * @throws \DomainException If the account would be overdrawn
     * @return void
     */
    public function debit(float $amount, string $description = '', string $transactionId = ''): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }
        
        if ($this->balance < $amount) {
            throw new \DomainException('Insufficient funds');
        }
        
        $newBalance = $this->balance - $amount;
        
        $event = new AccountDebitedEvent(
            $this->id,
            $this->version + 1,
            $amount,
            $newBalance,
            $description,
            $transactionId
        );
        
        $this->apply($event);
    }
    
    /**
     * Credit (deposit) money to the account
     * 
     * @param float $amount The amount to credit
     * @param string $description The description of the transaction
     * @param string $transactionId The ID of the transaction
     * @throws \InvalidArgumentException If the amount is negative or zero
     * @return void
     */
    public function credit(float $amount, string $description = '', string $transactionId = ''): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }
        
        $newBalance = $this->balance + $amount;
        
        $event = new AccountCreditedEvent(
            $this->id,
            $this->version + 1,
            $amount,
            $newBalance,
            $description,
            $transactionId
        );
        
        $this->apply($event);
    }
    
    /**
     * Apply an event to the aggregate
     * 
     * @param Event $event The event to apply
     * @return void
     */
    protected function applyEvent(Event $event): void
    {
        $method = $this->getApplyMethod($event);
        
        if (method_exists($this, $method)) {
            $this->$method($event);
        }
    }
    
    /**
     * Get the method name to apply an event
     * 
     * @param Event $event The event
     * @return string
     */
    private function getApplyMethod(Event $event): string
    {
        $eventClass = get_class($event);
        $parts = explode('\\', $eventClass);
        $eventName = end($parts);
        
        return 'apply' . $eventName;
    }
    
    /**
     * Apply an AccountCreatedEvent
     * 
     * @param AccountCreatedEvent $event The event to apply
     * @return void
     */
    private function applyAccountCreatedEvent(AccountCreatedEvent $event): void
    {
        $this->accountNumber = $event->getAccountNumber();
        $this->accountHolderName = $event->getAccountHolderName();
        $this->accountType = $event->getAccountType();
        $this->balance = $event->getInitialBalance();
        $this->currency = $event->getCurrency();
    }
    
    /**
     * Apply an AccountDebitedEvent
     * 
     * @param AccountDebitedEvent $event The event to apply
     * @return void
     */
    private function applyAccountDebitedEvent(AccountDebitedEvent $event): void
    {
        $this->balance = $event->getBalanceAfterDebit();
    }
    
    /**
     * Apply an AccountCreditedEvent
     * 
     * @param AccountCreditedEvent $event The event to apply
     * @return void
     */
    private function applyAccountCreditedEvent(AccountCreditedEvent $event): void
    {
        $this->balance = $event->getBalanceAfterCredit();
    }
    
    /**
     * Get the account number
     * 
     * @return string
     */
    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }
    
    /**
     * Get the account holder name
     * 
     * @return string
     */
    public function getAccountHolderName(): string
    {
        return $this->accountHolderName;
    }
    
    /**
     * Get the account type
     * 
     * @return string
     */
    public function getAccountType(): string
    {
        return $this->accountType;
    }
    
    /**
     * Get the current balance
     * 
     * @return float
     */
    public function getBalance(): float
    {
        return $this->balance;
    }
    
    /**
     * Get the currency
     * 
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }
}
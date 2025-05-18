<?php

namespace EventSourcingArchitecture\Projections;

use EventSourcingArchitecture\Events\Event;
use EventSourcingArchitecture\Events\AccountCreatedEvent;
use EventSourcingArchitecture\Events\AccountDebitedEvent;
use EventSourcingArchitecture\Events\AccountCreditedEvent;

/**
 * AccountBalanceProjection
 * 
 * This class provides a read model for account balances.
 * It projects events related to account creation, debiting, and crediting.
 */
class AccountBalanceProjection implements Projection
{
    /**
     * @var string The name of the projection
     */
    private string $name = 'account_balance';
    
    /**
     * @var array The account balances, indexed by account ID
     */
    private array $balances = [];
    
    /**
     * @var array Account details, indexed by account ID
     */
    private array $accounts = [];
    
    /**
     * @var array Transaction history, indexed by account ID
     */
    private array $transactions = [];
    
    /**
     * Project an event
     * 
     * @param Event $event The event to project
     * @return void
     */
    public function project(Event $event): void
    {
        if ($event instanceof AccountCreatedEvent) {
            $this->projectAccountCreated($event);
        } elseif ($event instanceof AccountDebitedEvent) {
            $this->projectAccountDebited($event);
        } elseif ($event instanceof AccountCreditedEvent) {
            $this->projectAccountCredited($event);
        }
    }
    
    /**
     * Project an AccountCreatedEvent
     * 
     * @param AccountCreatedEvent $event The event to project
     * @return void
     */
    private function projectAccountCreated(AccountCreatedEvent $event): void
    {
        $aggregateId = $event->getAggregateId();
        $initialBalance = $event->getInitialBalance();
        
        // Store the account balance
        $this->balances[$aggregateId] = $initialBalance;
        
        // Store account details
        $this->accounts[$aggregateId] = [
            'account_id' => $aggregateId,
            'account_number' => $event->getAccountNumber(),
            'account_holder_name' => $event->getAccountHolderName(),
            'account_type' => $event->getAccountType(),
            'currency' => $event->getCurrency(),
            'created_at' => $event->getOccurredAt()->format('Y-m-d H:i:s'),
            'balance' => $initialBalance
        ];
        
        // Initialize transaction history
        $this->transactions[$aggregateId] = [];
        
        // Add initial deposit as a transaction if balance is positive
        if ($initialBalance > 0) {
            $this->transactions[$aggregateId][] = [
                'transaction_id' => 'INITIAL-' . substr(md5($aggregateId), 0, 8),
                'type' => 'credit',
                'amount' => $initialBalance,
                'balance_after' => $initialBalance,
                'description' => 'Initial deposit',
                'timestamp' => $event->getOccurredAt()->format('Y-m-d H:i:s')
            ];
        }
    }
    
    /**
     * Project an AccountDebitedEvent
     * 
     * @param AccountDebitedEvent $event The event to project
     * @return void
     */
    private function projectAccountDebited(AccountDebitedEvent $event): void
    {
        $aggregateId = $event->getAggregateId();
        $amount = $event->getAmount();
        $balanceAfterDebit = $event->getBalanceAfterDebit();
        
        // Update the account balance
        $this->balances[$aggregateId] = $balanceAfterDebit;
        
        // Update account details
        if (isset($this->accounts[$aggregateId])) {
            $this->accounts[$aggregateId]['balance'] = $balanceAfterDebit;
        }
        
        // Add to transaction history
        if (!isset($this->transactions[$aggregateId])) {
            $this->transactions[$aggregateId] = [];
        }
        
        $this->transactions[$aggregateId][] = [
            'transaction_id' => $event->getTransactionId(),
            'type' => 'debit',
            'amount' => $amount,
            'balance_after' => $balanceAfterDebit,
            'description' => $event->getDescription(),
            'timestamp' => $event->getOccurredAt()->format('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Project an AccountCreditedEvent
     * 
     * @param AccountCreditedEvent $event The event to project
     * @return void
     */
    private function projectAccountCredited(AccountCreditedEvent $event): void
    {
        $aggregateId = $event->getAggregateId();
        $amount = $event->getAmount();
        $balanceAfterCredit = $event->getBalanceAfterCredit();
        
        // Update the account balance
        $this->balances[$aggregateId] = $balanceAfterCredit;
        
        // Update account details
        if (isset($this->accounts[$aggregateId])) {
            $this->accounts[$aggregateId]['balance'] = $balanceAfterCredit;
        }
        
        // Add to transaction history
        if (!isset($this->transactions[$aggregateId])) {
            $this->transactions[$aggregateId] = [];
        }
        
        $this->transactions[$aggregateId][] = [
            'transaction_id' => $event->getTransactionId(),
            'type' => 'credit',
            'amount' => $amount,
            'balance_after' => $balanceAfterCredit,
            'description' => $event->getDescription(),
            'timestamp' => $event->getOccurredAt()->format('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Reset the projection
     * 
     * @return void
     */
    public function reset(): void
    {
        $this->balances = [];
        $this->accounts = [];
        $this->transactions = [];
    }
    
    /**
     * Get the name of the projection
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Get the event types that this projection is interested in
     * 
     * @return array
     */
    public function getEventTypes(): array
    {
        return [
            'account_created_event',
            'account_debited_event',
            'account_credited_event'
        ];
    }
    
    /**
     * Check if this projection can handle the given event
     * 
     * @param Event $event The event to check
     * @return bool
     */
    public function canHandle(Event $event): bool
    {
        return in_array($event->getEventType(), $this->getEventTypes());
    }
    
    /**
     * Get the balance of an account
     * 
     * @param string $accountId The ID of the account
     * @return float|null
     */
    public function getBalance(string $accountId): ?float
    {
        return $this->balances[$accountId] ?? null;
    }
    
    /**
     * Get all account balances
     * 
     * @return array
     */
    public function getAllBalances(): array
    {
        return $this->balances;
    }
    
    /**
     * Get account details
     * 
     * @param string $accountId The ID of the account
     * @return array|null
     */
    public function getAccountDetails(string $accountId): ?array
    {
        return $this->accounts[$accountId] ?? null;
    }
    
    /**
     * Get all account details
     * 
     * @return array
     */
    public function getAllAccounts(): array
    {
        return $this->accounts;
    }
    
    /**
     * Get transaction history for an account
     * 
     * @param string $accountId The ID of the account
     * @return array
     */
    public function getTransactionHistory(string $accountId): array
    {
        return $this->transactions[$accountId] ?? [];
    }
}
<?php

namespace EventSourcingArchitecture\Events;

/**
 * AccountDebitedEvent
 * 
 * This class represents an event that occurs when money is withdrawn from a bank account.
 */
class AccountDebitedEvent extends Event
{
    /**
     * Constructor
     * 
     * @param string $aggregateId The ID of the bank account
     * @param int $aggregateVersion The version of the aggregate after this event is applied
     * @param float $amount The amount that was debited
     * @param float $balanceAfterDebit The balance after the debit operation
     * @param string $description The description of the transaction
     * @param string $transactionId The ID of the transaction
     */
    public function __construct(
        string $aggregateId,
        int $aggregateVersion,
        float $amount,
        float $balanceAfterDebit,
        string $description = '',
        string $transactionId = ''
    ) {
        $data = [
            'amount' => $amount,
            'balance_after_debit' => $balanceAfterDebit,
            'description' => $description,
            'transaction_id' => $transactionId ?: $this->generateTransactionId()
        ];
        
        parent::__construct($aggregateId, $aggregateVersion, $data);
    }
    
    /**
     * Get the amount that was debited
     * 
     * @return float
     */
    public function getAmount(): float
    {
        return $this->getData()['amount'];
    }
    
    /**
     * Get the balance after the debit operation
     * 
     * @return float
     */
    public function getBalanceAfterDebit(): float
    {
        return $this->getData()['balance_after_debit'];
    }
    
    /**
     * Get the description of the transaction
     * 
     * @return string
     */
    public function getDescription(): string
    {
        return $this->getData()['description'];
    }
    
    /**
     * Get the ID of the transaction
     * 
     * @return string
     */
    public function getTransactionId(): string
    {
        return $this->getData()['transaction_id'];
    }
    
    /**
     * Generate a transaction ID
     * 
     * @return string
     */
    private function generateTransactionId(): string
    {
        return 'TXN-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 10));
    }
}
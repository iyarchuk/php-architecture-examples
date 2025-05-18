<?php

namespace EventSourcingArchitecture\Events;

/**
 * AccountCreditedEvent
 * 
 * This class represents an event that occurs when money is deposited into a bank account.
 */
class AccountCreditedEvent extends Event
{
    /**
     * Constructor
     * 
     * @param string $aggregateId The ID of the bank account
     * @param int $aggregateVersion The version of the aggregate after this event is applied
     * @param float $amount The amount that was credited
     * @param float $balanceAfterCredit The balance after the credit operation
     * @param string $description The description of the transaction
     * @param string $transactionId The ID of the transaction
     */
    public function __construct(
        string $aggregateId,
        int $aggregateVersion,
        float $amount,
        float $balanceAfterCredit,
        string $description = '',
        string $transactionId = ''
    ) {
        $data = [
            'amount' => $amount,
            'balance_after_credit' => $balanceAfterCredit,
            'description' => $description,
            'transaction_id' => $transactionId ?: $this->generateTransactionId()
        ];
        
        parent::__construct($aggregateId, $aggregateVersion, $data);
    }
    
    /**
     * Get the amount that was credited
     * 
     * @return float
     */
    public function getAmount(): float
    {
        return $this->getData()['amount'];
    }
    
    /**
     * Get the balance after the credit operation
     * 
     * @return float
     */
    public function getBalanceAfterCredit(): float
    {
        return $this->getData()['balance_after_credit'];
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
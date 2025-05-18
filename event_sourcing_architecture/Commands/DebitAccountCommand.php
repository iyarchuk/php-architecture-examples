<?php

namespace EventSourcingArchitecture\Commands;

/**
 * DebitAccountCommand
 * 
 * This class represents a command to debit (withdraw) money from a bank account.
 */
class DebitAccountCommand implements Command
{
    /**
     * @var string The ID of the account to debit
     */
    private string $accountId;
    
    /**
     * @var float The amount to debit
     */
    private float $amount;
    
    /**
     * @var string The description of the transaction
     */
    private string $description;
    
    /**
     * @var string|null The ID of the transaction
     */
    private ?string $transactionId;
    
    /**
     * @var int|null The expected version of the aggregate
     */
    private ?int $expectedVersion;
    
    /**
     * Constructor
     * 
     * @param string $accountId The ID of the account to debit
     * @param float $amount The amount to debit
     * @param string $description The description of the transaction
     * @param string|null $transactionId The ID of the transaction
     * @param int|null $expectedVersion The expected version of the aggregate
     */
    public function __construct(
        string $accountId,
        float $amount,
        string $description = '',
        ?string $transactionId = null,
        ?int $expectedVersion = null
    ) {
        $this->accountId = $accountId;
        $this->amount = $amount;
        $this->description = $description;
        $this->transactionId = $transactionId;
        $this->expectedVersion = $expectedVersion;
    }
    
    /**
     * Get the name of the command
     * 
     * @return string
     */
    public function getCommandName(): string
    {
        return 'debit_account';
    }
    
    /**
     * Get the ID of the aggregate that this command targets
     * 
     * @return string|null
     */
    public function getAggregateId(): ?string
    {
        return $this->accountId;
    }
    
    /**
     * Get the expected version of the aggregate
     * 
     * @return int|null
     */
    public function getExpectedVersion(): ?int
    {
        return $this->expectedVersion;
    }
    
    /**
     * Get the command data
     * 
     * @return array
     */
    public function getData(): array
    {
        $data = [
            'account_id' => $this->accountId,
            'amount' => $this->amount,
            'description' => $this->description
        ];
        
        if ($this->transactionId !== null) {
            $data['transaction_id'] = $this->transactionId;
        }
        
        return $data;
    }
    
    /**
     * Validate the command
     * 
     * @throws \InvalidArgumentException If the command is invalid
     * @return void
     */
    public function validate(): void
    {
        if (empty($this->accountId)) {
            throw new \InvalidArgumentException('Account ID cannot be empty');
        }
        
        if ($this->amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }
    }
    
    /**
     * Get the account ID
     * 
     * @return string
     */
    public function getAccountId(): string
    {
        return $this->accountId;
    }
    
    /**
     * Get the amount to debit
     * 
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }
    
    /**
     * Get the description of the transaction
     * 
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
    
    /**
     * Get the ID of the transaction
     * 
     * @return string|null
     */
    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }
}
<?php

namespace EventSourcingArchitecture\Commands;

/**
 * CreateAccountCommand
 * 
 * This class represents a command to create a new bank account.
 */
class CreateAccountCommand implements Command
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
     * @var float The initial balance of the account
     */
    private float $initialBalance;
    
    /**
     * @var string The currency of the account (e.g., "USD", "EUR")
     */
    private string $currency;
    
    /**
     * Constructor
     * 
     * @param string $accountNumber The account number
     * @param string $accountHolderName The name of the account holder
     * @param string $accountType The type of account (e.g., "checking", "savings")
     * @param float $initialBalance The initial balance of the account
     * @param string $currency The currency of the account (e.g., "USD", "EUR")
     */
    public function __construct(
        string $accountNumber,
        string $accountHolderName,
        string $accountType,
        float $initialBalance = 0.0,
        string $currency = 'USD'
    ) {
        $this->accountNumber = $accountNumber;
        $this->accountHolderName = $accountHolderName;
        $this->accountType = $accountType;
        $this->initialBalance = $initialBalance;
        $this->currency = $currency;
    }
    
    /**
     * Get the name of the command
     * 
     * @return string
     */
    public function getCommandName(): string
    {
        return 'create_account';
    }
    
    /**
     * Get the ID of the aggregate that this command targets
     * 
     * @return string|null
     */
    public function getAggregateId(): ?string
    {
        return null; // No aggregate ID for creation commands
    }
    
    /**
     * Get the expected version of the aggregate
     * 
     * @return int|null
     */
    public function getExpectedVersion(): ?int
    {
        return null; // No expected version for creation commands
    }
    
    /**
     * Get the command data
     * 
     * @return array
     */
    public function getData(): array
    {
        return [
            'account_number' => $this->accountNumber,
            'account_holder_name' => $this->accountHolderName,
            'account_type' => $this->accountType,
            'initial_balance' => $this->initialBalance,
            'currency' => $this->currency
        ];
    }
    
    /**
     * Validate the command
     * 
     * @throws \InvalidArgumentException If the command is invalid
     * @return void
     */
    public function validate(): void
    {
        if (empty($this->accountNumber)) {
            throw new \InvalidArgumentException('Account number cannot be empty');
        }
        
        if (empty($this->accountHolderName)) {
            throw new \InvalidArgumentException('Account holder name cannot be empty');
        }
        
        if (empty($this->accountType)) {
            throw new \InvalidArgumentException('Account type cannot be empty');
        }
        
        if ($this->initialBalance < 0) {
            throw new \InvalidArgumentException('Initial balance cannot be negative');
        }
        
        if (empty($this->currency)) {
            throw new \InvalidArgumentException('Currency cannot be empty');
        }
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
     * Get the initial balance
     * 
     * @return float
     */
    public function getInitialBalance(): float
    {
        return $this->initialBalance;
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
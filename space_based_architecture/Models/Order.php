<?php

namespace SpaceBasedArchitecture\Models;

/**
 * Order tuple for the space-based architecture example.
 * Represents an order in the system.
 */
class Order extends Tuple
{
    /**
     * The ID of the customer who placed the order.
     *
     * @var string
     */
    protected $customerId;

    /**
     * The items in the order.
     *
     * @var array
     */
    protected $items = [];

    /**
     * The total amount of the order.
     *
     * @var float
     */
    protected $totalAmount = 0.0;

    /**
     * The status of the order.
     *
     * @var string
     */
    protected $status = 'pending';

    /**
     * Constructor.
     *
     * @param string $customerId The ID of the customer who placed the order
     * @param array $items The items in the order
     * @param string|null $id The unique identifier of the order (optional)
     */
    public function __construct(string $customerId, array $items = [], ?string $id = null)
    {
        parent::__construct($id);
        $this->customerId = $customerId;
        $this->items = $items;
        $this->calculateTotalAmount();
    }

    /**
     * Get the customer ID.
     *
     * @return string The customer ID
     */
    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    /**
     * Get the items in the order.
     *
     * @return array The items
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Add an item to the order.
     *
     * @param array $item The item to add
     * @return void
     */
    public function addItem(array $item): void
    {
        $this->items[] = $item;
        $this->calculateTotalAmount();
    }

    /**
     * Remove an item from the order.
     *
     * @param int $index The index of the item to remove
     * @return void
     */
    public function removeItem(int $index): void
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items); // Re-index the array
            $this->calculateTotalAmount();
        }
    }

    /**
     * Get the total amount of the order.
     *
     * @return float The total amount
     */
    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    /**
     * Calculate the total amount of the order.
     *
     * @return void
     */
    protected function calculateTotalAmount(): void
    {
        $this->totalAmount = 0.0;
        foreach ($this->items as $item) {
            $this->totalAmount += $item['price'] * $item['quantity'];
        }
    }

    /**
     * Get the status of the order.
     *
     * @return string The status
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Set the status of the order.
     *
     * @param string $status The status
     * @return void
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * Convert the order to an array.
     *
     * @return array The order as an array
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'customerId' => $this->customerId,
            'items' => $this->items,
            'totalAmount' => $this->totalAmount,
            'status' => $this->status,
        ]);
    }

    /**
     * Create an order from an array.
     *
     * @param array $data The data to create the order from
     * @return static The created order
     */
    public static function fromArray(array $data): self
    {
        $order = new static(
            $data['customerId'] ?? '',
            $data['items'] ?? [],
            $data['id'] ?? null
        );
        
        if (isset($data['createdAt'])) {
            $order->createdAt = $data['createdAt'];
        }
        
        if (isset($data['status'])) {
            $order->status = $data['status'];
        }
        
        if (isset($data['totalAmount'])) {
            $order->totalAmount = $data['totalAmount'];
        }
        
        return $order;
    }
}
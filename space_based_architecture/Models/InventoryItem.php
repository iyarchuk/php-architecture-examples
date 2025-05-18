<?php

namespace SpaceBasedArchitecture\Models;

/**
 * InventoryItem tuple for the space-based architecture example.
 * Represents an item in the inventory.
 */
class InventoryItem extends Tuple
{
    /**
     * The product ID.
     *
     * @var string
     */
    protected $productId;

    /**
     * The name of the product.
     *
     * @var string
     */
    protected $name;

    /**
     * The price of the product.
     *
     * @var float
     */
    protected $price;

    /**
     * The quantity in stock.
     *
     * @var int
     */
    protected $quantity;

    /**
     * The description of the product.
     *
     * @var string
     */
    protected $description;

    /**
     * Constructor.
     *
     * @param string $productId The product ID
     * @param string $name The name of the product
     * @param float $price The price of the product
     * @param int $quantity The quantity in stock
     * @param string $description The description of the product
     * @param string|null $id The unique identifier of the inventory item (optional)
     */
    public function __construct(
        string $productId,
        string $name,
        float $price,
        int $quantity,
        string $description = '',
        ?string $id = null
    ) {
        parent::__construct($id);
        $this->productId = $productId;
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->description = $description;
    }

    /**
     * Get the product ID.
     *
     * @return string The product ID
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * Get the name of the product.
     *
     * @return string The name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the price of the product.
     *
     * @return float The price
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Set the price of the product.
     *
     * @param float $price The price
     * @return void
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * Get the quantity in stock.
     *
     * @return int The quantity
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Set the quantity in stock.
     *
     * @param int $quantity The quantity
     * @return void
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * Increase the quantity in stock.
     *
     * @param int $amount The amount to increase
     * @return void
     */
    public function increaseQuantity(int $amount): void
    {
        $this->quantity += $amount;
    }

    /**
     * Decrease the quantity in stock.
     *
     * @param int $amount The amount to decrease
     * @return bool True if the quantity was decreased, false if there's not enough stock
     */
    public function decreaseQuantity(int $amount): bool
    {
        if ($this->quantity >= $amount) {
            $this->quantity -= $amount;
            return true;
        }
        return false;
    }

    /**
     * Get the description of the product.
     *
     * @return string The description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the description of the product.
     *
     * @param string $description The description
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Check if the item is in stock.
     *
     * @param int $quantity The quantity to check
     * @return bool True if the item is in stock, false otherwise
     */
    public function isInStock(int $quantity = 1): bool
    {
        return $this->quantity >= $quantity;
    }

    /**
     * Convert the inventory item to an array.
     *
     * @return array The inventory item as an array
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'productId' => $this->productId,
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'description' => $this->description,
        ]);
    }

    /**
     * Create an inventory item from an array.
     *
     * @param array $data The data to create the inventory item from
     * @return static The created inventory item
     */
    public static function fromArray(array $data): self
    {
        $item = new static(
            $data['productId'] ?? '',
            $data['name'] ?? '',
            $data['price'] ?? 0.0,
            $data['quantity'] ?? 0,
            $data['description'] ?? '',
            $data['id'] ?? null
        );
        
        if (isset($data['createdAt'])) {
            $item->createdAt = $data['createdAt'];
        }
        
        return $item;
    }
}
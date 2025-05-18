<?php

namespace SpaceBasedArchitecture\Models;

/**
 * Base class for tuples stored in the space.
 * A tuple is a data object that can be stored in the space.
 */
class Tuple
{
    /**
     * The unique identifier of the tuple.
     *
     * @var string
     */
    protected $id;

    /**
     * The timestamp when the tuple was created.
     *
     * @var int
     */
    protected $createdAt;

    /**
     * Constructor.
     *
     * @param string|null $id The unique identifier of the tuple (optional)
     */
    public function __construct(?string $id = null)
    {
        $this->id = $id ?? uniqid('tuple_');
        $this->createdAt = time();
    }

    /**
     * Get the unique identifier of the tuple.
     *
     * @return string The unique identifier
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the timestamp when the tuple was created.
     *
     * @return int The timestamp
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * Convert the tuple to an array.
     *
     * @return array The tuple as an array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'createdAt' => $this->createdAt,
        ];
    }

    /**
     * Create a tuple from an array.
     *
     * @param array $data The data to create the tuple from
     * @return static The created tuple
     */
    public static function fromArray(array $data): self
    {
        $tuple = new static($data['id'] ?? null);
        
        if (isset($data['createdAt'])) {
            $tuple->createdAt = $data['createdAt'];
        }
        
        return $tuple;
    }
}
<?php

namespace App\DTOs;

class CreateOrderDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly array $items
    ) {}

    public static function fromArray(array $data): self
    {
        $items = array_map(fn($item) => OrderItemDTO::fromArray($item), $data['items']);
        
        return new self(
            userId: $data['user_id'],
            items: $items
        );
    }
} 
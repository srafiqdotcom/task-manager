<?php
declare(strict_types=1);

namespace App\Application\DTO;

use JsonSerializable;

class PaginationResult implements JsonSerializable
{
    private array $items;
    private int $total;
    private int $page;
    private int $limit;
    private int $totalPages;

    public function __construct(
        array $items,
        int $total,
        int $page,
        int $limit,
        int $totalPages
    ) {
        $this->items = $items;
        $this->total = $total;
        $this->page = $page;
        $this->limit = $limit;
        $this->totalPages = $totalPages;
    }

    public function jsonSerialize(): array
    {
        return [
            'data' => $this->items,
            'meta' => [
                'pagination' => [
                    'total' => $this->total,
                    'per_page' => $this->limit,
                    'current_page' => $this->page,
                    'total_pages' => $this->totalPages
                ]
            ]
        ];
    }
}

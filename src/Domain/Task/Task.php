<?php
declare(strict_types=1);

namespace App\Domain\Task;

use JsonSerializable;

class Task implements JsonSerializable
{
    private ?int $id;
    private string $title;
    private ?string $description;
    private bool $completed;
    private string $created_at;

    public function __construct(
        ?int $id,
        string $title,
        ?string $description,
        bool $completed,
        string $created_at
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->completed = $completed;
        $this->created_at = $created_at;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'completed' => $this->completed,
            'created_at' => $this->created_at,
        ];
    }
}

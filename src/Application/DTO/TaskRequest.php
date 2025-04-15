<?php
declare(strict_types=1);

namespace App\Application\DTO;

class TaskRequest
{
    public ?string $title = null;
    public ?string $description = null;
    public ?bool $completed = null;
}

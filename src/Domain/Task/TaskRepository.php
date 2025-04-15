<?php
declare(strict_types=1);

namespace App\Domain\Task;

use App\Domain\Entity\Task;

interface TaskRepository
{
    /**
     * @param int $page
     * @param int $limit
     * @return Task[]
     */
    public function findAll(int $page = 1, int $limit = 10): array;

    /**
     * @return int
     */
    public function count(): int;

    /**
     * @param int $id
     * @return Task
     * @throws TaskNotFoundException
     */
    public function findById(int $id): Task;

    /**
     * @param Task $task
     * @return Task
     */
    public function save(Task $task): Task;

    /**
     * @param Task $task
     * @return bool
     */
    public function delete(Task $task): bool;
}

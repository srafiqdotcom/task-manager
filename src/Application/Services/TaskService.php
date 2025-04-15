<?php
declare(strict_types=1);

namespace App\Application\Services;

use App\Application\DTO\PaginationResult;
use App\Application\DTO\TaskRequest;
use App\Domain\Entity\Task;
use App\Domain\Task\TaskNotFoundException;
use App\Domain\Task\TaskRepository;

class TaskService
{
    private TaskRepository $taskRepository;
    private int $defaultLimit;
    private int $maxLimit;

    public function __construct(
        TaskRepository $taskRepository,
        int $defaultLimit = 10,
        int $maxLimit = 100
    ) {
        $this->taskRepository = $taskRepository;
        $this->defaultLimit = $defaultLimit;
        $this->maxLimit = $maxLimit;
    }

    public function getAllTasks(int $page = 1, int $limit = null): PaginationResult
    {
        $limit = $limit ?? $this->defaultLimit;
        $limit = min($limit, $this->maxLimit);
        $page = max(1, $page);

        $tasks = $this->taskRepository->findAll($page, $limit);
        $total = $this->taskRepository->count();

        return new PaginationResult(
            $tasks,
            $total,
            $page,
            $limit,
            (int)ceil($total / $limit)
        );
    }

    public function getTaskById(int $id): Task
    {
        return $this->taskRepository->findById($id);
    }

    public function createTask(TaskRequest $taskRequest): Task
    {
        $task = new Task(
            $taskRequest->title,
            $taskRequest->description,
            $taskRequest->completed
        );

        return $this->taskRepository->save($task);
    }

    public function updateTask(int $id, TaskRequest $taskRequest): Task
    {
        $task = $this->taskRepository->findById($id);

        if ($taskRequest->title !== null) {
            $task->setTitle($taskRequest->title);
        }

        if ($taskRequest->description !== null) {
            $task->setDescription($taskRequest->description);
        }

        if ($taskRequest->completed !== null) {
            $task->setCompleted($taskRequest->completed);
        }

        return $this->taskRepository->save($task);
    }

    public function deleteTask(int $id): bool
    {
        $task = $this->taskRepository->findById($id);

        return $this->taskRepository->delete($task);
    }
}

<?php
declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\DTO\PaginationResult;
use App\Application\DTO\TaskRequest;
use App\Application\Services\TaskService;
use App\Domain\Task\TaskNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

class TaskController
{
    private TaskService $taskService;
    private LoggerInterface $logger;

    public function __construct(TaskService $taskService, LoggerInterface $logger)
    {
        $this->taskService = $taskService;
        $this->logger = $logger;
    }

    public function listTasks(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $page = isset($queryParams['page']) ? (int) $queryParams['page'] : 1;
        $limit = isset($queryParams['limit']) ? (int) $queryParams['limit'] : 10;

        $result = $this->taskService->getAllTasks($page, $limit);

        $this->logger->info("Tasks list was viewed.");

        return $this->jsonResponse($response, $result);
    }

    public function getTask(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $task = $this->taskService->getTaskById($id);
            $this->logger->info("Task of id `{$id}` was viewed.");

            return $this->jsonResponse($response, $task);
        } catch (TaskNotFoundException $e) {
            throw new HttpBadRequestException($request, $e->getMessage());
        }
    }

    public function createTask(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $taskRequest = new TaskRequest();
        $taskRequest->title = $data['title'] ?? '';
        $taskRequest->description = $data['description'] ?? null;
        $taskRequest->completed = isset($data['completed']) ? (bool) $data['completed'] : false;

        $this->validateTaskRequest($request, $taskRequest);

        $task = $this->taskService->createTask($taskRequest);

        $this->logger->info("Task of id `{$task->getId()}` was created.");

        return $this->jsonResponse($response, $task, 201);
    }

    public function updateTask(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $data = $request->getParsedBody();

        $taskRequest = new TaskRequest();
        $taskRequest->title = $data['title'] ?? null;
        $taskRequest->description = $data['description'] ?? null;
        $taskRequest->completed = isset($data['completed']) ? (bool) $data['completed'] : null;

        if ($taskRequest->title !== null) {
            $this->validateTaskTitle($request, $taskRequest->title);
        }

        try {
            $task = $this->taskService->updateTask($id, $taskRequest);

            $this->logger->info("Task of id `{$id}` was updated.");

            return $this->jsonResponse($response, $task);
        } catch (TaskNotFoundException $e) {
            throw new HttpBadRequestException($request, $e->getMessage());
        }
    }

    public function deleteTask(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $this->taskService->deleteTask($id);

            $this->logger->info("Task of id `{$id}` was deleted.");

            return $response->withStatus(204);
        } catch (TaskNotFoundException $e) {
            throw new HttpBadRequestException($request, $e->getMessage());
        }
    }

    private function validateTaskRequest(Request $request, TaskRequest $taskRequest): void
    {
        $this->validateTaskTitle($request, $taskRequest->title);
    }

    private function validateTaskTitle(Request $request, string $title): void
    {
        if (empty($title) || strlen($title) < 3) {
            throw new HttpBadRequestException(
                $request,
                'Title is required and must be at least 3 characters long.'
            );
        }
    }

    private function jsonResponse(Response $response, $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}

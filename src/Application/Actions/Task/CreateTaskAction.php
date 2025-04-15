<?php
declare(strict_types=1);

namespace App\Application\Actions\Task;

use App\Application\Actions\ActionError;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class CreateTaskAction extends TaskAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $data = $this->getFormData();

        // Validate title
        if (!isset($data['title']) || strlen($data['title']) < 3) {
            throw new HttpBadRequestException(
                $this->request, 
                'Title is required and must be at least 3 characters long.'
            );
        }

        // Validate completed if present
        if (isset($data['completed']) && !is_bool($data['completed'])) {
            throw new HttpBadRequestException(
                $this->request, 
                'Completed must be a boolean value.'
            );
        }

        $task = $this->taskRepository->createTask($data);

        $this->logger->info("Task of id `{$task->getId()}` was created.");

        return $this->respondWithData($task, 201);
    }
}

<?php
declare(strict_types=1);

namespace App\Application\Actions\Task;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class UpdateTaskAction extends TaskAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $taskId = (int) $this->resolveArg('id');
        $data = $this->getFormData();

        // Validate title if present
        if (isset($data['title']) && strlen($data['title']) < 3) {
            throw new HttpBadRequestException(
                $this->request, 
                'Title must be at least 3 characters long.'
            );
        }

        // Validate completed if present
        if (isset($data['completed']) && !is_bool($data['completed'])) {
            throw new HttpBadRequestException(
                $this->request, 
                'Completed must be a boolean value.'
            );
        }

        $task = $this->taskRepository->updateTask($taskId, $data);

        $this->logger->info("Task of id `{$taskId}` was updated.");

        return $this->respondWithData($task);
    }
}

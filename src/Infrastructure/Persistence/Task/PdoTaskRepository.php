<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Task;

use App\Domain\Task\Task;
use App\Domain\Task\TaskNotFoundException;
use App\Domain\Task\TaskRepository;
use PDO;

class PdoTaskRepository implements TaskRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return Task[]
     */
    public function findAll(): array
    {
        $statement = $this->pdo->prepare('SELECT * FROM tasks ORDER BY id');
        $statement->execute();
        
        $tasks = [];
        while ($row = $statement->fetch()) {
            $tasks[] = new Task(
                (int) $row['id'],
                $row['title'],
                $row['description'],
                (bool) $row['completed'],
                $row['created_at']
            );
        }
        
        return $tasks;
    }

    /**
     * @param int $id
     * @return Task
     * @throws TaskNotFoundException
     */
    public function findTaskOfId(int $id): Task
    {
        $statement = $this->pdo->prepare('SELECT * FROM tasks WHERE id = :id');
        $statement->execute(['id' => $id]);
        
        $row = $statement->fetch();
        
        if (!$row) {
            throw new TaskNotFoundException();
        }
        
        return new Task(
            (int) $row['id'],
            $row['title'],
            $row['description'],
            (bool) $row['completed'],
            $row['created_at']
        );
    }

    /**
     * @param array $data
     * @return Task
     */
    public function createTask(array $data): Task
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO tasks (title, description, completed, created_at) 
             VALUES (:title, :description, :completed, :created_at) 
             RETURNING id, title, description, completed, created_at'
        );
        
        $created_at = date('Y-m-d\TH:i:s\Z');
        
        $statement->execute([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'completed' => $data['completed'] ?? false,
            'created_at' => $created_at,
        ]);
        
        $row = $statement->fetch();
        
        return new Task(
            (int) $row['id'],
            $row['title'],
            $row['description'],
            (bool) $row['completed'],
            $row['created_at']
        );
    }

    /**
     * @param int $id
     * @param array $data
     * @return Task
     * @throws TaskNotFoundException
     */
    public function updateTask(int $id, array $data): Task
    {
        // First check if the task exists
        $this->findTaskOfId($id);
        
        $fields = [];
        $values = ['id' => $id];
        
        if (isset($data['title'])) {
            $fields[] = 'title = :title';
            $values['title'] = $data['title'];
        }
        
        if (array_key_exists('description', $data)) {
            $fields[] = 'description = :description';
            $values['description'] = $data['description'];
        }
        
        if (isset($data['completed'])) {
            $fields[] = 'completed = :completed';
            $values['completed'] = $data['completed'];
        }
        
        if (empty($fields)) {
            // No fields to update
            return $this->findTaskOfId($id);
        }
        
        $sql = 'UPDATE tasks SET ' . implode(', ', $fields) . ' WHERE id = :id RETURNING id, title, description, completed, created_at';
        
        $statement = $this->pdo->prepare($sql);
        $statement->execute($values);
        
        $row = $statement->fetch();
        
        return new Task(
            (int) $row['id'],
            $row['title'],
            $row['description'],
            (bool) $row['completed'],
            $row['created_at']
        );
    }

    /**
     * @param int $id
     * @return bool
     * @throws TaskNotFoundException
     */
    public function deleteTask(int $id): bool
    {
        // First check if the task exists
        $this->findTaskOfId($id);
        
        $statement = $this->pdo->prepare('DELETE FROM tasks WHERE id = :id');
        $statement->execute(['id' => $id]);
        
        return true;
    }
}

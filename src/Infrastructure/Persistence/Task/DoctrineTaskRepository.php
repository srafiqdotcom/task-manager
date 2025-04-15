<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Task;

use App\Domain\Entity\Task;
use App\Domain\Task\TaskNotFoundException;
use App\Domain\Task\TaskRepository;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManager;
use Doctrine\ORM\EntityRepository;

class DoctrineTaskRepository implements TaskRepository
{
    private DoctrineEntityManager $doctrineEntityManager;
    private EntityRepository $repository;

    public function __construct(DoctrineEntityManager $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->repository = $doctrineEntityManager->getEntityManager()->getRepository(Task::class);
    }

    public function findAll(int $page = 1, int $limit = 10): array
    {
        $offset = ($page - 1) * $limit;

        return $this->repository->findBy(
            [],
            ['id' => 'ASC'],
            $limit,
            $offset
        );
    }

    public function count(): int
    {
        return $this->repository->count([]);
    }

    public function findById(int $id): Task
    {
        $task = $this->repository->find($id);

        if ($task === null) {
            throw new TaskNotFoundException();
        }

        return $task;
    }

    public function save(Task $task): Task
    {
        $this->doctrineEntityManager->getEntityManager()->persist($task);
        $this->doctrineEntityManager->getEntityManager()->flush();

        return $task;
    }

    public function delete(Task $task): bool
    {
        $this->doctrineEntityManager->getEntityManager()->remove($task);
        $this->doctrineEntityManager->getEntityManager()->flush();

        return true;
    }
}

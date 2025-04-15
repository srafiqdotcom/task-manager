<?php
declare(strict_types=1);

namespace Tests\Application\Services;

use App\Application\DTO\PaginationResult;
use App\Application\DTO\TaskRequest;
use App\Application\Services\TaskService;
use App\Domain\Entity\Task;
use App\Domain\Task\TaskNotFoundException;
use App\Domain\Task\TaskRepository;
use PHPUnit\Framework\TestCase;

class TaskServiceTest extends TestCase
{
    public function testGetAllTasks()
    {
        $task = new Task('Test Task', 'Test Description', false);
        
        $taskRepository = $this->createMock(TaskRepository::class);
        $taskRepository->method('findAll')->willReturn([$task]);
        $taskRepository->method('count')->willReturn(1);
        
        $taskService = new TaskService($taskRepository);
        $result = $taskService->getAllTasks();
        
        $this->assertInstanceOf(PaginationResult::class, $result);
        $this->assertEquals([$task], $result->jsonSerialize()['data']);
        $this->assertEquals(1, $result->jsonSerialize()['meta']['pagination']['total']);
    }
    
    public function testGetTaskById()
    {
        $task = new Task('Test Task', 'Test Description', false);
        
        $taskRepository = $this->createMock(TaskRepository::class);
        $taskRepository->method('findById')->willReturn($task);
        
        $taskService = new TaskService($taskRepository);
        $result = $taskService->getTaskById(1);
        
        $this->assertSame($task, $result);
    }
    
    public function testGetTaskByIdNotFound()
    {
        $taskRepository = $this->createMock(TaskRepository::class);
        $taskRepository->method('findById')->willThrowException(new TaskNotFoundException());
        
        $taskService = new TaskService($taskRepository);
        
        $this->expectException(TaskNotFoundException::class);
        $taskService->getTaskById(999);
    }
    
    public function testCreateTask()
    {
        $taskRequest = new TaskRequest();
        $taskRequest->title = 'New Task';
        $taskRequest->description = 'New Description';
        $taskRequest->completed = false;
        
        $task = new Task('New Task', 'New Description', false);
        
        $taskRepository = $this->createMock(TaskRepository::class);
        $taskRepository->method('save')->willReturn($task);
        
        $taskService = new TaskService($taskRepository);
        $result = $taskService->createTask($taskRequest);
        
        $this->assertSame($task, $result);
    }
}

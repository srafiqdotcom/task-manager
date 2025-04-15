<?php
declare(strict_types=1);

namespace Tests\Domain\Task;

use App\Domain\Entity\Task;
use DateTime;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testGetters()
    {
        $task = new Task('Test Task', 'Test Description', true);
        
        $this->assertEquals('Test Task', $task->getTitle());
        $this->assertEquals('Test Description', $task->getDescription());
        $this->assertTrue($task->isCompleted());
        
        $this->assertInstanceOf(DateTime::class, $task->getCreatedAt());
    }

    public function testJsonSerialize()
    {
        $task = new Task('Test Task', 'Test Description', true);
        $json = $task->jsonSerialize();
        
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayHasKey('title', $json);
        $this->assertArrayHasKey('description', $json);
        $this->assertArrayHasKey('completed', $json);
        $this->assertArrayHasKey('created_at', $json);
        
        $this->assertEquals('Test Task', $json['title']);
        $this->assertEquals('Test Description', $json['description']);
        $this->assertTrue($json['completed']);
    }
}

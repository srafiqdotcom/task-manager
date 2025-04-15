<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class TaskSeeder extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'title' => 'Buy groceries',
                'description' => 'Milk, Bread, Eggs',
                'completed' => false,
                'created_at' => '2025-03-24 14:00:00'
            ],
            [
                'title' => 'Finish project',
                'description' => 'Complete the PHP + PostgreSQL test',
                'completed' => false,
                'created_at' => '2025-03-24 15:00:00'
            ]
        ];

        $tasks = $this->table('tasks');
        $tasks->insert($data)->save();
    }
}

<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => true,
            'logError'            => true,
            'logErrorDetails'     => true,
            'logger' => [
                'name' => 'slim-app',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
            'doctrine' => [
                'dev_mode' => true,
                'cache_dir' => __DIR__ . '/../var/cache/doctrine',
                'metadata_dirs' => [__DIR__ . '/../src/Domain/Entity'],
                'connection' => [
                    'driver' => 'pdo_pgsql',
                    'host' => $_ENV['DB_HOST'] ?? 'db',
                    'port' => $_ENV['DB_PORT'] ?? '5432',
                    'dbname' => $_ENV['DB_DATABASE'] ?? 'slim_tasks',
                    'user' => $_ENV['DB_USERNAME'] ?? 'postgres',
                    'password' => $_ENV['DB_PASSWORD'] ?? 'password',
                    'charset' => 'utf8'
                ]
            ],
            'api_keys' => [
                'valid_keys' => explode(',', $_ENV['API_KEYS'] ?? '427bb67d-07f8-4220-a11a-7dfb181e9bdc,ea7853e9-0a36-4b21-beac-860fc94d9680')
            ],
            'pagination' => [
                'default_limit' => 10,
                'max_limit' => 100
            ]
        ],
    ]);
};

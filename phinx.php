<?php

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'pgsql',
            'host' => getenv('DB_HOST') ?: 'db',
            'name' => getenv('DB_DATABASE') ?: 'slim_tasks',
            'user' => getenv('DB_USERNAME') ?: 'postgres',
            'pass' => getenv('DB_PASSWORD') ?: 'password',
            'port' => getenv('DB_PORT') ?: '5432',
            'charset' => 'utf8',
        ],
    ],
    'version_order' => 'creation',
];

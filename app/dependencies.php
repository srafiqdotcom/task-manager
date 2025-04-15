<?php
declare(strict_types=1);

use App\Application\Controllers\TaskController;
use App\Application\Services\TaskService;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManager;
use DI\ContainerBuilder;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

        EntityManager::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');
            $doctrineSettings = $settings['doctrine'];

            $config = Setup::createAnnotationMetadataConfiguration(
                $doctrineSettings['metadata_dirs'],
                $doctrineSettings['dev_mode'],
                $doctrineSettings['cache_dir']
            );

            $config->setMetadataDriverImpl(
                new AnnotationDriver(
                    new AnnotationReader,
                    $doctrineSettings['metadata_dirs']
                )
            );

            return EntityManager::create(
                $doctrineSettings['connection'],
                $config
            );
        },

        DoctrineEntityManager::class => function (ContainerInterface $c) {
            return new DoctrineEntityManager($c->get(EntityManager::class));
        },

        TaskService::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');
            return new TaskService(
                $c->get(App\Domain\Task\TaskRepository::class),
                $settings['pagination']['default_limit'],
                $settings['pagination']['max_limit']
            );
        },

        TaskController::class => function (ContainerInterface $c) {
            return new TaskController(
                $c->get(TaskService::class),
                $c->get(LoggerInterface::class)
            );
        }
    ]);
};

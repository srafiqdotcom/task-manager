<?php
declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap the container
$containerBuilder = new \DI\ContainerBuilder();
$settings = require __DIR__ . '/app/settings.php';
$settings($containerBuilder);

$dependencies = require __DIR__ . '/app/dependencies.php';
$dependencies($containerBuilder);

$repositories = require __DIR__ . '/app/repositories.php';
$repositories($containerBuilder);

$container = $containerBuilder->build();

/** @var EntityManagerInterface $entityManager */
$entityManager = $container->get(\Doctrine\ORM\EntityManager::class);

return ConsoleRunner::createHelperSet($entityManager);

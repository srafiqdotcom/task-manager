<?php
declare(strict_types=1);

namespace Tests;

use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    protected function getAppInstance()
    {
        // Load settings
        $settings = require __DIR__ . '/../app/settings.php';
        
        // Create Container
        $containerBuilder = new ContainerBuilder();
        $settings($containerBuilder);
        
        // Build container
        $container = $containerBuilder->build();
        
        // Create app
        $app = \Slim\Factory\AppFactory::createFromContainer($container);
        
        // Register middleware
        $middleware = require __DIR__ . '/../app/middleware.php';
        $middleware($app);
        
        // Register routes
        $routes = require __DIR__ . '/../app/routes.php';
        $routes($app);
        
        return $app;
    }
}

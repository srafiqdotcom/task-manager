<?php
declare(strict_types=1);

use App\Application\Middleware\ApiKeyAuthMiddleware;
use App\Application\Middleware\JsonBodyParserMiddleware;
use Slim\App;

return function (App $app) {
    // Add JSON parser globally
    $app->add(JsonBodyParserMiddleware::class);

    // Register the API key auth middleware
    $container = $app->getContainer();
    $settings = $container->get('settings');
    $validApiKeys = $settings['api_keys']['valid_keys'];

    $app->add(new ApiKeyAuthMiddleware($validApiKeys));
};

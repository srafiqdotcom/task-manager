<?php
declare(strict_types=1);

use App\Application\Controllers\TaskController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    // Root route
    $app->get('/', function ($request, $response) {
        $payload = json_encode(['message' => 'Slim app is running']);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Healthcheck
    $app->get('/healthcheck', function ($request, $response) {
        $payload = json_encode(['status' => 'ok']);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Task routes
    $app->group('/tasks', function (Group $group) {
        $group->get('', [TaskController::class, 'listTasks']);
        $group->get('/{id}', [TaskController::class, 'getTask']);
        $group->post('', [TaskController::class, 'createTask']);
        $group->put('/{id}', [TaskController::class, 'updateTask']);
        $group->delete('/{id}', [TaskController::class, 'deleteTask']);
    });

    // OpenAPI documentation
    $app->get('/docs', function ($request, $response) {
        $yamlFile = __DIR__ . '/../openapi.yaml';

        if (!file_exists($yamlFile)) {
            $response->getBody()->write("openapi.yaml not found at: $yamlFile");
            return $response->withStatus(500)->withHeader('Content-Type', 'text/plain');
        }

        $yaml = file_get_contents($yamlFile);
        $response->getBody()->write($yaml);
        return $response->withHeader('Content-Type', 'text/yaml');
    });

    // Swagger UI
    $app->get('/docs/ui', function ($request, $response) {
        $htmlFile = __DIR__ . '/../public/swagger-ui.html';

        if (!file_exists($htmlFile)) {
            $response->getBody()->write("swagger-ui.html not found.");
            return $response->withStatus(500)->withHeader('Content-Type', 'text/plain');
        }

        $html = file_get_contents($htmlFile);
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    });
};

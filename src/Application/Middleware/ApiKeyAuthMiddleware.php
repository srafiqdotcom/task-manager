<?php
declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class ApiKeyAuthMiddleware implements MiddlewareInterface
{
    private array $validApiKeys;
    private array $excludedPaths = ['/docs', '/docs/ui'];

    public function __construct(array $validApiKeys)
    {
        $this->validApiKeys = $validApiKeys;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        
        // Skip authentication for excluded paths
        foreach ($this->excludedPaths as $excludedPath) {
            if (strpos($path, $excludedPath) === 0) {
                return $handler->handle($request);
            }
        }
        
        $apiKey = $request->getHeaderLine('X-API-Key');
        
        if (empty($apiKey) || !in_array($apiKey, $this->validApiKeys)) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'error' => 'Unauthorized',
                'message' => 'Invalid or missing API key'
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }
        
        return $handler->handle($request);
    }
}

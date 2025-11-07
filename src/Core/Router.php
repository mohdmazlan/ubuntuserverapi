<?php

declare(strict_types=1);

namespace UbuntuServerAPI\Core;

class Router
{
    private array $routes = [];

    public function __construct(private readonly Request $request)
    {
    }

    public function get(string $path, callable|array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, callable|array $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, callable|array $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, callable|array $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function resolve(): Response
    {
        $method = $this->request->getMethod();
        $uri = $this->request->getPath();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->convertPathToPattern($route['path']);
            
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match
                
                $handler = $route['handler'];
                
                if (is_array($handler)) {
                    [$controller, $method] = $handler;
                    $controllerInstance = new $controller();
                    return call_user_func_array([$controllerInstance, $method], $matches);
                }
                
                return call_user_func_array($handler, $matches);
            }
        }

        return new Response(['error' => 'Route not found'], 404);
    }

    private function convertPathToPattern(string $path): string
    {
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
}


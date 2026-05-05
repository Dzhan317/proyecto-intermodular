<?php
declare(strict_types=1);

/**
 * Router
 * Maps incoming HTTP requests to Controller@method handlers.
 * Supports named URL parameters via /:param syntax.
 */
class Router
{
    private array $routes = [];

    public function get(string $path, string $handler): void  { $this->addRoute('GET',  $path, $handler); }
    public function post(string $path, string $handler): void { $this->addRoute('POST', $path, $handler); }

    private function addRoute(string $method, string $path, string $handler): void
    {
        $this->routes[] = ['method' => $method, 'path' => $path, 'handler' => $handler];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri    = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            $pattern = $this->buildPattern($route['path']);
            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->callHandler($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        $this->render404();
    }

    private function buildPattern(string $path): string
    {
        $pattern = preg_replace('/\/:([a-zA-Z_]+)/', '/(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function callHandler(string $handler, array $params): void
    {
        [$controllerName, $method] = explode('@', $handler);
        $file = APP_PATH . '/Controllers/' . $controllerName . '.php';
        if (!file_exists($file)) throw new \RuntimeException("Controller not found: {$controllerName}");
        require_once $file;
        $controller = new $controllerName();
        $controller->$method($params);
    }

    private function render404(): void
    {
        $view = APP_PATH . '/Views/errors/404.php';
        if (file_exists($view)) require_once $view;
        else echo '<h1>404 &mdash; Page not found</h1>';
    }
}

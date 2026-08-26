<?php

namespace App\Core;

class Router {
    protected $routes = [];
    protected $middleware = [];

    public function get($path, $callback, $middleware = []) {
        $this->addRoute('GET', $path, $callback, $middleware);
    }

    public function post($path, $callback, $middleware = []) {
        $this->addRoute('POST', $path, $callback, $middleware);
    }

    public function put($path, $callback, $middleware = []) {
        $this->addRoute('PUT', $path, $callback, $middleware);
    }

    public function delete($path, $callback, $middleware = []) {
        $this->addRoute('DELETE', $path, $callback, $middleware);
    }

    protected function addRoute($method, $path, $callback, $middleware) {
        $this->routes[$method][$this->convertToRegex($path)] = [
            'callback' => $callback,
            'middleware' => (array) $middleware
        ];
    }

    protected function convertToRegex($path) {
        // Garante que o path comece com / e não termine com / (exceto se for apenas /)
        $path = '/' . trim($path, '/');
        $regex = preg_replace('/\{([a-z0-9\-]+)\}/', '(?P<$1>[a-z0-9\-]+)', $path);
        return '#^' . $regex . '$#';
    }

    public function resolve() {
        $path = $_SERVER['REQUEST_URI'];
        $path = parse_url($path, PHP_URL_PATH);
        
        // Remove a pasta base do projeto dinamicamente
        $basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        if ($basePath !== '/') {
            $path = substr($path, strlen($basePath));
        }
        
        // Remove index.php se estiver presente
        $path = str_replace('/index.php', '', $path);
        
        $path = '/' . trim($path, '/');
        $method = $_SERVER['REQUEST_METHOD'];

        // Support for _method in POST to simulate PUT/DELETE
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        if (!isset($this->routes[$method])) {
            $this->abort(405);
        }

        foreach ($this->routes[$method] as $regex => $route) {
            if (preg_match($regex, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                // Execute Middlewares
                foreach ($route['middleware'] as $middleware) {
                    $this->executeMiddleware($middleware);
                }

                $callback = $route['callback'];

                if (is_string($callback)) {
                    $parts = explode('@', $callback);
                    $controllerName = "\\App\\Controllers\\" . $parts[0];
                    $action = $parts[1];

                    if (!class_exists($controllerName)) {
                        throw new \Exception("Controller $controllerName not found");
                    }

                    $controller = new $controllerName();
                    return call_user_func_array([$controller, $action], $params);
                }

                return call_user_func_array($callback, $params);
            }
        }

        $this->abort(404);
    }

    protected function executeMiddleware($middleware) {
        $middlewareClass = "\\App\\Middlewares\\" . $middleware;
        if (class_exists($middlewareClass)) {
            $instance = new $middlewareClass();
            $instance->handle();
        } else {
            throw new \Exception("Middleware $middlewareClass not found");
        }
    }

    protected function abort($code = 404) {
        http_response_code($code);
        // You could load a custom error page here
        echo "$code Not Found";
        exit;
    }
}

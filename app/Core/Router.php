<?php declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $action)
    {
        $this->add('GET', $path, $action);
        return $this;
    }

    public function post(string $path, array $action)
    {
        $this->add('POST', $path, $action);
        return $this;
    }

    private function add(string $method, string $path, array $action)
    {
        $this->routes[] = compact('method', 'path', 'action');
    }

    public function dispatch(Request $request, Application $app)
    {
        $path = $request->getPath();
        $method = $request->getMethod();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([^/]+)', $route['path']);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $path, $matches)) {

                array_shift($matches);

                [$controllerClass, $methodName] = $route['action'];

                $controller = $app->make($controllerClass);

                foreach ($matches as $k => $v) {
                    if (is_numeric($v)) {
                        $matches[$k] = (int) $v;
                    }
                };
                return $controller->$methodName($request, ...$matches);
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
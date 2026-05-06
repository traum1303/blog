<?php declare(strict_types=1);

namespace App\Core;

use Dotenv\Dotenv;
use PDO;
use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;
use Smarty;

class Application
{
    private array $bindings = [];
    private array $singletons = [];
    private array $routes = [];
    protected string $basePath;
    protected array $config = [];

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function bootstrap(): void
    {
        $this->loadEnv();
        $this->loadConfig();
        $this->registerDatabase();
        $this->registerSmarty();
    }

    public function create(): static
    {
        foreach ($this->routes as $route) {
            $routePath = $this->basePath . '/' . ltrim($route, '/');

            if (!is_file($routePath) || !is_readable($routePath)) {
                throw new RuntimeException("Route file not found or not readable: {$routePath}");
            }

            require $routePath;
        }
        return $this;
    }

    public function handleRequest($request, $router)
    {
        $router->dispatch($request, $this);
    }


    public function get(string $class)
    {
        if (array_key_exists($class, $this->singletons) && $this->singletons[$class] !== null) {
            return $this->singletons[$class];
        }

        if (array_key_exists($class, $this->bindings) && $this->bindings[$class] !== null) {
            return $this->bindings[$class];
        }

        return null;
    }

    public function config(string $key)
    {
        return $this->config[$key] ?? null;
    }

    public function register(string $provider, ...$params): void
    {
        $provider::register($this, ...$params);
    }

    public function bind(string $abstract, string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function singleton(string $abstract, $concrete): void
    {
        if ($concrete instanceof \Closure) {
            $this->singletons[$abstract] = $concrete->call($this);
            return;
        }
        $this->singletons[$abstract] = $concrete;
    }

    public function make(string $abstract)
    {
        if (array_key_exists($abstract, $this->singletons) && $this->singletons[$abstract] !== null) {
            return $this->singletons[$abstract];
        }

        if (isset($this->bindings[$abstract])) {
            $concrete = $this->bindings[$abstract];

            if ($concrete instanceof \Closure) {
                $object = $concrete($this);

            } elseif (is_string($concrete)) {
                $object = $this->resolve($concrete);

            } elseif (is_object($concrete)) {
                $object = $concrete;
            } else {
                throw new \Exception("Invalid binding for {$abstract}");
            }
        } else {
            $object = $this->resolve($abstract);
        }
        if (array_key_exists($abstract, $this->singletons)) {
            $this->singletons[$abstract] = $object;
        }

        return $object;
    }

    public function loadRoutes(...$routes): static
    {
        $this->routes = $routes;
        return $this;
    }

    protected function loadEnv(): void
    {
        $dotenv = Dotenv::createImmutable($this->basePath);
        $dotenv->safeLoad();
    }

    protected function loadConfig(): void
    {
        $requiredEnv = [
            'DB_HOST',
            'DB_PORT',
            'DB_DATABASE',
            'DB_USERNAME',
            'DB_PASSWORD',
            'DB_CONNECTION',
        ];

        foreach ($requiredEnv as $key) {
            if (!array_key_exists($key, $_ENV)) {
                throw new RuntimeException("Missing required environment variable: {$key}");
            }
        }

        $this->config['db'] = [
            'host' => $_ENV['DB_HOST'],
            'port' => $_ENV['DB_PORT'],
            'database' => $_ENV['DB_DATABASE'],
            'username' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
            'connection' => $_ENV['DB_CONNECTION'],
        ];
    }

    protected function registerDatabase(): void
    {
        $config = $this->config['db'];

        $this->singleton(PDO::class, function () use ($config) {
            $dsn = "{$config['connection']}:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";

            return new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        });
    }

    protected function registerSmarty(): void
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $currentPath = parse_url($requestUri, PHP_URL_PATH) ?: '/';

        $this->singleton(Smarty::class, function () use ($currentPath) {
            $smarty = new Smarty();
            $smarty->assign('currentPath', $currentPath)
                ->setTemplateDir($this->basePath . '/templates')
                ->setCompileDir($this->basePath . '/storage/cache')
                ->setCacheDir($this->basePath . '/cache')
                ->setConfigDir($this->basePath . '/config')
                ->registerPlugin('modifier', 'truncate_text', 'truncateText')
                ->registerPlugin('function', 'is_active', 'isActiveByUrl');

            return $smarty;
        });
    }

    /**
     * @throws \ReflectionException
     */
    private function resolve(string $class)
    {
        $reflection = new ReflectionClass($class);

        if (!$reflection->isInstantiable()) {
            throw new \Exception("Class {$class} is not instantiable");
        }

        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return new $class;
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();

            if (!$type instanceof ReflectionNamedType) {
                throw new \Exception("Cannot resolve {$param->getName()}");
            }

            if ($type->isBuiltin()) {
                throw new \Exception("Cannot resolve primitive {$param->getName()}");
            }

            $dependencies[] = $this->make($type->getName());
        }

        return $reflection->newInstanceArgs($dependencies);
    }

}
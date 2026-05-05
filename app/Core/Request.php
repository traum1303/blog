<?php declare(strict_types=1);

namespace App\Core;

class Request
{
    public static function capture(): Request
    {
        return new self();
    }

    public function getPath(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        $path = parse_url($uri, PHP_URL_PATH);

        return rtrim($path, '/') ?: '/';
    }

    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function input(string $key, $default = null)
    {
        return $_REQUEST[$key] ?? $default;
    }

    public function query(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }
}
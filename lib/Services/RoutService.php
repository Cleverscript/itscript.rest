<?php

namespace Itscript\Rest\Services;

use Itscript\Rest\Entities\Route;

class RoutService
{
    public function isAllowed($requestUri, $apiRootPath): bool
    {
        return (!str_contains($requestUri, $apiRootPath))? false : true;
    }

    public function getRoute(string $method, string $apiRootPath, string $requestUri): Route
    {
        $apiRootPath = rtrim($apiRootPath, '/');

        // TODO: переписать на запрос раута через слой репозитория
        $routes = [
            ['GET', '/users', 'UserController@index'],
            ['GET', '/users/{id}', 'UserController@show'],
            ['POST', '/users', 'UserController@store'],
            ['PUT', '/users/{id}', 'UserController@update'],
            ['DELETE', '/users/{id}', 'UserController@destroy'],
        ];

        $path = preg_replace("#^{$apiRootPath}#", '', rtrim($requestUri, '/'));

        foreach ($routes as $route) {
            $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([a-zA-Z0-9_-]+)', $route[1]);
            $pattern = "#^" . $pattern . "$#";

            /*echo '<pre>';
            print_r([$route[1], $pattern, $path]);
            echo '</pre>';*/

            if ($route[0] !== $method || !preg_match($pattern, $path, $matches)) {
                continue;
            }

            array_shift($matches);

            [$method, $routePath, $handler] = $route;
            [$controller, $function] = explode('@', $handler);

            return new Route(
                $method,
                $routePath,
                $controller,
                $function,
                $matches
            );
        }
    }
}
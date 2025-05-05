<?php

namespace Itscript\Rest\Services;

use Bitrix\Main\Context;
use Bitrix\Main\DI\ServiceLocator;
use Itscript\Rest\Entities\Route;

class RoutService
{
    public function getRoute(): Route
    {
        $moduleService = ServiceLocator::getInstance()->get(ITSCRIPT_REST_MID . '.ModuleService');

        $request = Context::getCurrent()->getRequest();
        $requestUri = $request->getRequestUri();

        $apiRootPath = rtrim($moduleService->getPropVal('ITSCRIPT_REST_ROOT_PATH'), '/');

        // TODO: переписать на запрос раута через слой репозитория
        $routes = [
            ['GET', '/users', 'UserController@index'],
            ['GET', '/users/{id}', 'UserController@show'],
            ['GET', '/users/group/{id}/count/{cnt}', 'UserController@listByGroup'],
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

            if (!preg_match($pattern, $path, $matches)) {
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
<?php

namespace Itscript\Rest\Services;

use Bitrix\Main\Context;
use Itscript\Rest\Entities\Route;
use Bitrix\Main\DI\ServiceLocator;
use Itscript\Rest\Repositories\RoutRepository;
use Itscript\Rest\Exceptions\RoutNotFoundException;

class RoutService
{
    public function getRoute(): Route
    {
        $result = null;
        $moduleService = ServiceLocator::getInstance()->get(ITSCRIPT_REST_MID . '.ModuleService');

        $request = Context::getCurrent()->getRequest();
        $requestUri = $request->getRequestUri();
        $method = $request->getRequestMethod();

        $apiRootPath = rtrim($moduleService->getPropVal('ITSCRIPT_REST_ROOT_PATH'), '/');

        $routes = RoutRepository::getRoutes($method);

        if (empty($routes)) {
            throw new RoutNotFoundException();
        }

        $path = preg_replace("#^{$apiRootPath}#", '', rtrim($requestUri, '/'));

        foreach ($routes as $route) {
            $route = array_values($route);
            [$method, $routePath, $handler] = $route;
            $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([a-zA-Z0-9_-]+)', $route[1]);
            $pattern = "#^" . $pattern . "$#";

            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }

            array_shift($matches);

            [$controller, $function] = explode('@', $handler);

            $result = new Route(
                $method,
                $routePath,
                $controller,
                $function,
                $matches
            );
        }

        if (empty($result)) {
            throw new RoutNotFoundException();
        }

        return $result;
    }
}

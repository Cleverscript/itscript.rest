<?php

namespace Itscript\Rest\Services;

use Itscript\Rest\Entities\Route;
use Itscript\Rest\Contracts\BaseControllerInterface;
use Itscript\Rest\Exceptions\ControllerNotFoundException;

class ControllerService
{
    private Route $route;
    private BaseControllerInterface $controllerInstance;

    public function setInstance(Route $route): self
    {
        $this->route = $route;

        $classFilePath = __DIR__ . "/../Controllers/{$route->controller}.php";
        $className = "\Itscript\Rest\Controllers\\$route->controller";

        if (!file_exists($classFilePath)) {
            throw new ControllerNotFoundException();
        }

        require_once $classFilePath;

        if (!class_exists($className)) {
            throw new ControllerNotFoundException();
        }

        $this->controllerInstance = new $className;

        return $this;
    }

    public function callAction()
    {
        return call_user_func_array([$this->controllerInstance, $this->route->function], $this->route->params);
    }
}
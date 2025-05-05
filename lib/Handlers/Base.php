<?php

namespace Itscript\Rest\Handlers;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\DI\ServiceLocator;
use Itscript\Rest\Exceptions\RequestMethodException;
use Itscript\Rest\Helpers\ResponseHelper;
use Itscript\Rest\Helpers\ExceptionHelper;
use Itscript\Rest\Exceptions\RequestUriException;

Loc::loadMessages(__FILE__);

class Base
{
    public static function init()
    {
        try {
            $serviceLocator = ServiceLocator::getInstance();

            // Получаем rout
            $routService = $serviceLocator->get(ITSCRIPT_REST_MID . '.RoutService');
            $route = $routService->getRoute();

            // Проверяем реквест на соответсвие настрокам раута (метод, авторизация, e.t.c)
            $middlewareService = $serviceLocator->get(ITSCRIPT_REST_MID . '.Middleware');
            $middlewareService->checkRequestUri()->checkMethod($route);

            // Получаем из раута объект контроллера и вызываем его метод
            $controllerService = $serviceLocator->get(ITSCRIPT_REST_MID . '.ControllerService');
            $data = $controllerService->setInstance($route)->callAction();

            ResponseHelper::set200($data);

        } catch (RequestUriException) {
            // Показываем web page
        } catch (RequestMethodException) {
            ResponseHelper::set405();
        } catch (\Throwable $e) {
            ResponseHelper::set500();
            ExceptionHelper::writeToLog($e->getMessage());
        }
    }
}
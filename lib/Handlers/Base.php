<?php

namespace Itscript\Rest\Handlers;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\DI\ServiceLocator;
use Itscript\Rest\Helpers\ResponseHelper;
use Itscript\Rest\Exceptions\RequestUriException;
use Itscript\Rest\Exceptions\BadRequestException;
use Itscript\Rest\Exceptions\RoutNotFoundException;
use Itscript\Rest\Exceptions\AuthLoginFailException;
use Itscript\Rest\Exceptions\RequestMethodException;

Loc::loadMessages(__FILE__);

class Base
{
    public static function init()
    {
        try {
            $serviceLocator = ServiceLocator::getInstance();

            $middlewareService = $serviceLocator->get(ITSCRIPT_REST_MID . '.Middleware');

            // Проверяем путь в реквесте на наличе в нем корневого API раута
            $middlewareService->checkRequestUri();

            // Получаем rout
            $routService = $serviceLocator->get(ITSCRIPT_REST_MID . '.RoutService');
            $route = $routService->getRoute();

            // Проверяем реквест на соответсвие настрокам раута (метод, авторизация, e.t.c)
            $middlewareService->setRoute($route)
                ->checkRequestUri()
                ->checkMethod()
                ->checkAuth();

            // Получаем из раута объект контроллера и вызываем его метод
            $controllerService = $serviceLocator->get(ITSCRIPT_REST_MID . '.ControllerService');
            $data = $controllerService->setInstance($route)->callAction();

            ResponseHelper::set200($data);

        } catch (RequestUriException) {
            // Показываем web page
        } catch (BadRequestException) {
            ResponseHelper::set400();
        } catch (RoutNotFoundException) {
            ResponseHelper::set404();
        } catch (RequestMethodException) {
            ResponseHelper::set405();
        } catch (AuthLoginFailException) {
            ResponseHelper::set401();
        } catch (\Throwable $e) {
            ResponseHelper::set500();
            ExceptionHelper::writeToLog($e->getMessage());
        }
    }
}

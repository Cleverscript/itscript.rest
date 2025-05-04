<?php

namespace Itscript\Rest\Handlers;

use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\DI\ServiceLocator;
use Itscript\Rest\Helpers\ResponseHelper;
use Itscript\Rest\Helpers\ExceptionHelper;
use Itscript\Rest\Exceptions\ModuleSettingsException;

Loc::loadMessages(__FILE__);

class Base
{
    public static function init()
    {
        try {
            $serviceLocator = ServiceLocator::getInstance();

            if (!$serviceLocator->has(ITSCRIPT_REST_MID . '.ModuleService')) {
                return false;
            }

            if (!$serviceLocator->has(ITSCRIPT_REST_MID . '.RoutService')) {
                return false;
            }

            $moduleService = $serviceLocator->get(ITSCRIPT_REST_MID . '.ModuleService');

            $request = Context::getCurrent()->getRequest();

            if ($request->isAdminSection()) {
                return false;
            }

            $requestMethod = $request->getRequestMethod();
            $requestUri = $request->getRequestUri();

            $apiRootPath = $moduleService->getPropVal('ITSCRIPT_REST_ROOT_PATH');

            if (empty($apiRootPath)) {
                throw new ModuleSettingsException(
                    Loc::getMessage('ITSCRIPT_REST_ROOT_PATH_EMPTY', ['#MID#' => ITSCRIPT_REST_MID])
                );
            }

            $routService = $serviceLocator->get(ITSCRIPT_REST_MID . '.RoutService');

            if (!$routService->isAllowed($requestUri, $apiRootPath)) {
                //ResponseHelper::set404();
                return false;
            }

            $route = $routService->getRoute($requestMethod, $apiRootPath, $requestUri);

            $controllerService = $serviceLocator->get(ITSCRIPT_REST_MID . '.ControllerService');
            $data = $controllerService->setInstance($route)->callAction();

            ResponseHelper::set200($data);

        } catch (\Throwable $e) {
            ResponseHelper::set500();
            ExceptionHelper::writeToLog($e->getMessage());
        }
    }
}
<?php

namespace Itscript\Rest\Handlers;

use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\DI\ServiceLocator;
use Itscript\Rest\Helpers\ExceptionHelper;

Loc::loadMessages(__FILE__);

class Base
{
    public static function init()
    {
        $serviceLocator = ServiceLocator::getInstance();

        if ($serviceLocator->has(ITSCRIPT_REST_MID . '.ModuleService')) {
            return false;
        }

        $moduleService = $serviceLocator->get(ITSCRIPT_REST_MID . '.ModuleService');

        $request = Context::getCurrent()->getRequest();
        $requestUri = $request->getRequestUri();
        $defaultRootPath = $moduleService->getPropVal('ITSCRIPT_REST_ROOT_PATH');

        if (empty($defaultRootPath)) {
            ExceptionHelper::writeToLog(
                Loc::getMessage('ITSCRIPT_REST_ROOT_PATH_EMPTY', ['#MID#' => ITSCRIPT_REST_MID])
            );
            return false;
        }

        if (str_contains($requestUri, $defaultRootPath)) {
            return false;
        }

        //die();
    }
}
<?php

namespace Itscript\Rest\Services;

use Bitrix\Main\Context;
use Itscript\Rest\Entities\Route;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\DI\ServiceLocator;
use Itscript\Rest\Exceptions\RequestUriException;
use Itscript\Rest\Exceptions\RequestMethodException;
use Itscript\Rest\Exceptions\ModuleSettingsException;

Loc::loadMessages(__FILE__);

class Middleware
{
    protected $request;
    protected $apiRootPath;

    public function __construct()
    {
        $moduleService = ServiceLocator::getInstance()->get(ITSCRIPT_REST_MID . '.ModuleService');

        $this->request = Context::getCurrent()->getRequest();
        $this->apiRootPath = $moduleService->getPropVal('ITSCRIPT_REST_ROOT_PATH');

        if (empty($this->apiRootPath)) {
            throw new ModuleSettingsException(
                Loc::getMessage('ITSCRIPT_REST_ROOT_PATH_EMPTY', ['#MID#' => ITSCRIPT_REST_MID])
            );
        }
    }

    public function checkRequestUri()
    {
        if ($this->request->isAdminSection() || !str_contains($this->request->getRequestUri(), $this->apiRootPath)) {
            throw new RequestUriException();
        }

        return $this;
    }

    public function checkMethod(Route $route)
    {
        if ($this->request->getRequestMethod() !== $route->method) {
            throw new RequestMethodException();
        }

        return $this;
    }
}

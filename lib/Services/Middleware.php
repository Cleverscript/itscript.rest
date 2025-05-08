<?php

namespace Itscript\Rest\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Bitrix\Main\Context;
use Itscript\Rest\Entities\Route;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\DI\ServiceLocator;
use Itscript\Rest\Exceptions\RequestUriException;
use Itscript\Rest\Exceptions\BadRequestException;
use Itscript\Rest\Exceptions\AuthLoginFailException;
use Itscript\Rest\Exceptions\RequestMethodException;
use Itscript\Rest\Exceptions\ModuleSettingsException;

Loc::loadMessages(__FILE__);

class Middleware
{
    protected $route;
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

    public function setRoute(Route $route)
    {
        $this->route = $route;

        return $this;
    }

    public function checkMethod()
    {
        if ($this->request->getRequestMethod() !== $this->route->method) {
            throw new RequestMethodException();
        }

        return $this;
    }

    public function checkAuth()
    {
        if (str_contains($this->route->path, '/Auth')) {
            return $this;
        }

        $moduleService = ServiceLocator::getInstance()->get(ITSCRIPT_REST_MID . '.ModuleService');
        $key = $moduleService->getPropVal('ITSCRIPT_REST_SECRET_KEY');
        $alg = $moduleService->getPropVal('ITSCRIPT_REST_ENCRYPTION_ALG');

        $headers = getallheaders();
        $authHeader = $headers['Authorization'];

        if (empty($authHeader)) {
            throw new BadRequestException('Authorization header not passed');
        }

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $jwt = $matches[1];
        }

        if (empty($jwt)) {
            throw new BadRequestException('The authorization header does not contain JWT token');
        }

        try {
            $decoded = JWT::decode(
                $jwt,
                new Key($key, $alg)
            );

            if (!$decoded->userData->id) {
                throw new \Exception("JWT token is not contained in user ID");
            }

        } catch (\Throwable $e) {
            throw new AuthLoginFailException($e->getMessage());
        }

        return $this;
    }
}

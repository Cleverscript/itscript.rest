<?php

namespace Itscript\Rest\Traits;

use Firebase\JWT\JWT;
use Bitrix\Main\DI\ServiceLocator;
use Itscript\Rest\Exceptions\BadRequestException;
use Itscript\Rest\Exceptions\AuthLoginFailException;

trait JwtAuthTrait
{
    public function getToken(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data)) {
            throw new BadRequestException();
        }

        $moduleService = ServiceLocator::getInstance()->get(ITSCRIPT_REST_MID . '.ModuleService');

        $params = [
            'LOGIN' => $data['login'],
            'PASSWORD' => $data['password'],
            'PASSWORD_ORIGINAL' => 'Y',
        ];

        $userId = (int) \CUser::LoginInternal(
            $params,
            $message
        );

        if (!$userId) {
            throw new AuthLoginFailException($message['MESSAGE']);
        }

        $arrUser  = \CUser::GetByID($userId)->Fetch();
        $userName = $arrUser['LAST_NAME'] . ' ' . $arrUser['NAME'] . ' ' . $arrUser['SECOND_NAME'];
        $userEmail = $arrUser['EMAIL'];

        $now = time();
        $alg = $moduleService->getPropVal('ITSCRIPT_REST_ENCRYPTION_ALG');
        $key = $moduleService->getPropVal('ITSCRIPT_REST_SECRET_KEY');
        $exp = $now + $moduleService->getPropVal('ITSCRIPT_REST_JWT_LIFETIME');

        $userData = [
            "id" => $userId,
            "name" => $userName,
            "email" => $userEmail
        ];

        $jwt = JWT::encode(
            [
                "iss"  => $moduleService->getPropVal('ITSCRIPT_REST_JWT_ISS'),
                "aud"  => $moduleService->getPropVal('ITSCRIPT_REST_JWT_AUD'),
                "iat"  => $now,
                "nbf"  => $now,
                "exp"  => $exp,
                "userData" => $userData
            ],
            $key,
            $alg
        );

        return [
            "jwt" => $jwt,
            "userData" => $userData,
        ];
    }
}

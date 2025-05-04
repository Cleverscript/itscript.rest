<?php

namespace Itscript\Rest\Helpers;

class ResponseHelper
{
    public static function set200(array|object $data): string
    {
        header('Content-Type: application/json');
        http_response_code(200);
        die(self::encode($data));
    }

    public static function set404(): string
    {
        header('Content-Type: application/json');
        http_response_code(404);
        die(self::encode(['error' => 'Route not found']));
    }

    public static function set500(): string
    {
        header('Content-Type: application/json');
        http_response_code(500);
        die(self::encode([
            'error' => 'Internal Server Error',
            'message' => 'An unexpected error occurred. Please try again later.'
        ]));
    }

    public static function encode(array|object $data): string
    {
        return json_encode($data);
    }
}
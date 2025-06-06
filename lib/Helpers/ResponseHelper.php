<?php

namespace Itscript\Rest\Helpers;

class ResponseHelper
{
    public static function set200(array|object $data): void
    {
        self::response(200, $data);
    }

    public static function set201(array|object $data): void
    {
        if (is_array($data)) {
            $data = array_merge([
                'status' => 'success',
                'message' => 'User created successfully.'
            ], $data);
        }

        self::response(201, $data);
    }

    public static function set400(string $message = ''): void
    {
        self::response(400, [
            'status' => 'error',
            'message' => "Bad request. {$message}"
        ]);
    }
    public static function set401(string $message = ''): void
    {
        self::response(401, [
            'status' => 'error',
            'message' => "Unauthorized. {$message}"
        ]);
    }

    public static function set404(string $message = ''): void
    {
        self::response(404, [
            'status' => 'error',
            'message' => 'The requested resource was not found. {$message}'
        ]);
    }

    public static function set405(string $message = ''): void
    {
        self::response(405, [
            'status' => 'error',
            'message' => "Method Not Allowed. {$message}"
        ]);
    }

    public static function set500(string $message = ''): void
    {
        self::response(500, [
            'status' => 'error',
            'message' => "Internal Server Error."
        ]);
    }

    public static function encode(array|object $data): string
    {
        return json_encode($data);
    }

    private static function response(int $code, array|object $data)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        die(self::encode($data));
    }
}

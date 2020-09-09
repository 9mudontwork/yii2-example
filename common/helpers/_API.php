<?php

// muhammad

namespace common\components;

use common\components\_;

class _API
{
    private static $enableError = true;

    public static $data = null,
        $code = null,
        $message = null,
        $error = null;

    public static function getResponse()
    {
        $response = [
            'results' => [
                'content_code' => self::$code,
                'message' => self::$message,
                'data' => self::$data,
                'error' => self::$error,
            ]
        ];

        if (!self::$enableError)
            unset($response['results']['error']);

        return $response;
    }

    private static function getMessageCode($code)
    {
        $messageCode = [
            200 => 'OK',

            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',

            500 => 'Internal Server Error',
        ];

        return isset($messageCode[$code]) ? $messageCode[$code] : null;
    }

    public static function response(array $data)
    {
        _::setResponseAsJson();
        foreach ($data as $key => $value) {
            switch ($key) {

                case 'data':
                    if ($value) {
                        self::$data = $value;
                    }
                    break;

                case 'code':
                    if ($value) {
                        $message = self::getMessageCode($value);
                        if ($message) {
                            self::$message = $message;
                        }
                        self::$code = $value;
                    }
                    break;

                case 'message':
                    if ($value) {
                        self::$message = $value;
                    }
                    break;

                case 'error':
                    if ($value && self::$enableError) {
                        self::$error = $value;
                    }
                    break;
            }
        }
        return self::getResponse();
    }
}

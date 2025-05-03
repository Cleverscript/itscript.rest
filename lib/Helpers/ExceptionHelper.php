<?php

namespace Itscript\Rest\Helpers;

use Bitrix\Main\Application;
use Bitrix\Main\SystemException;

class ExceptionHelper
{
    public static function writeToLog(string $mess)
    {
        $exception = new SystemException($mess);
        $application = Application::getInstance();
        $exceptionHandler = $application->getExceptionHandler();
        $exceptionHandler->writeToLog($exception);
    }
}
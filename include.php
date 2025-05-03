<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

const ITSCRIPT_REST_MID = "itscript.rest";

const DEPENDENCE_MODULE = [];

$defaultOptions = Option::getDefaults(ITSCRIPT_REST_MID);

$defaultRestRootPath = Option::get(ITSCRIPT_REST_MID, 'ITSCRIPT_REST_ROOT_PATH', $defaultOptions['ITSCRIPT_REST_ROOT_PATH']);

Loc::loadMessages(__FILE__);

foreach (DEPENDENCE_MODULE as $module) {
    if (!Loader::includeModule($module)) {
        throw new \Exception(Loc::getMessage(
            "ITSCRIPT_REST_MODULE_IS_NOT_INSTALLED",
            ['#MODULE_ID#' => $module]
        ));
    }
}
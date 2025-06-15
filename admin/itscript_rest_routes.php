<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
require_once dirname(__FILE__) . "/../include.php";
require_once dirname(__FILE__) . "/../prolog.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php";

use Bitrix\Main\Localization\Loc;

global $APPLICATION;
$APPLICATION->SetTitle(Loc::getMessage('ITSCRIPT_REST_ADM_PAGE_LOG_TITLE'));

$APPLICATION->IncludeComponent(
	"itscript:rest_routes_grid",
	"",
	Array(
		"NUM_PAGE" => "10"
	)
);



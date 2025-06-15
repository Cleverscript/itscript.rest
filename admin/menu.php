<?php
use Bitrix\Main\Localization\Loc;

IncludeModuleLangFile(__FILE__);

define('MODULE_ID', 'itscript.rest');
$vendorId = current(explode('.', MODULE_ID));

global $APPLICATION, $adminMenu;

if ($APPLICATION->GetGroupRight(MODULE_ID)!="D") {
    $arMenu = [
        "parent_menu" => "global_menu_services",
        "section" => MODULE_ID,
        "sort" => 1,
        "text" => Loc::getMessage('ITSCRIPT_REST_MENU_ROOT_NAME'),
        "title" => Loc::getMessage('ITSCRIPT_REST_MENU_ROOT_NAME'),
        "icon" => "	landing_menu_icon",
        "page_icon" => "landing_menu_icon",
        "module_id" => MODULE_ID,
        "items_id" => "menu_{$vendorId}",
        'items' => [
            [
                'text' => Loc::getMessage('ITSCRIPT_REST_ROUTES'),
                'icon' => 'default_menu_icon',
                'page_icon' => 'constructor-menu-icon-blocks-templates',
                'url' => '/bitrix/admin/itscript_rest_routes.php',
                'more_url' => [],
                'items_id' => 'main'
            ],
        ]
    ];

    return $arMenu;
}

return false;
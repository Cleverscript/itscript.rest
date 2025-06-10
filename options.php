<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;

$module_id = "itscript.rest";

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/options.php');
IncludeModuleLangFile(__FILE__);

Loader::includeModule($module_id);

global $APPLICATION;

$request = HttpApplication::getInstance()->getContext()->getRequest();

$defaultOptions = Option::getDefaults($module_id);

$arMainPropsTab = [
    "DIV" => "edit1",
    "TAB" => Loc::getMessage("ITSCRIPT_REST_MAIN_TAB_SETTINGS"),
    "TITLE" => Loc::getMessage("ITSCRIPT_REST_MAIN_TAB_SETTINGS_TITLE"),
    "OPTIONS" => [
        [
            "ITSCRIPT_REST_ROOT_PATH",
            Loc::getMessage("ITSCRIPT_REST_ROOT_PATH"),
            $defaultOptions["ITSCRIPT_REST_ROOT_PATH"],
            [
                "text",
                100
            ]
        ],
        [
            "ITSCRIPT_REST_ENCRYPTION_ALG",
            Loc::getMessage("ITSCRIPT_REST_ENCRYPTION_ALG"),
            $defaultOptions["ITSCRIPT_REST_ENCRYPTION_ALG"],
            [
                "selectbox",
                ['HS256' => 'HS256']
            ]
        ],
        [
            "ITSCRIPT_REST_SECRET_KEY",
            Loc::getMessage("ITSCRIPT_REST_SECRET_KEY"),
            $defaultOptions["ITSCRIPT_REST_SECRET_KEY"],
            [
                "text",
                100
            ]
        ],
        [
            "ITSCRIPT_REST_JWT_LIFETIME",
            Loc::getMessage("ITSCRIPT_REST_JWT_LIFETIME"),
            $defaultOptions["ITSCRIPT_REST_JWT_LIFETIME"],
            [
                "text",
                100
            ]
        ],
        [
            "ITSCRIPT_REST_JWT_ISS",
            Loc::getMessage("ITSCRIPT_REST_JWT_ISS"),
            $defaultOptions["ITSCRIPT_REST_JWT_ISS"],
            [
                "text",
                100
            ]
        ],
        [
            "ITSCRIPT_REST_JWT_AUD",
            Loc::getMessage("ITSCRIPT_REST_JWT_AUD"),
            $defaultOptions["ITSCRIPT_REST_JWT_AUD"],
            [
                "text",
                100
            ]
        ]
    ]
];

$aTabs = [
    $arMainPropsTab,
    [
        "DIV" => "edit2",
        "TAB" => Loc::getMessage("MAIN_TAB_RIGHTS"),
        "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS")
    ],
];
?>

<?php
//Save form
if ($request->isPost() && $request["save"] && check_bitrix_sessid()) {
    foreach ($aTabs as $aTab) {
        if (!empty($aTab['OPTIONS'])) {
            __AdmSettingsSaveOptions($module_id, $aTab["OPTIONS"]);
        }
    }
}
?>

<!-- FORM TAB -->
<?php
$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>
<?php $tabControl->Begin(); ?>
<form method="post" action="<?=$APPLICATION->GetCurPage();?>?mid=<?=htmlspecialcharsbx($request["mid"]);?>&amp;lang=<?=LANGUAGE_ID?>" name="<?=$module_id;?>">
    <?php $tabControl->BeginNextTab(); ?>

    <?php
    foreach ($aTabs as $aTab) {
        if(is_array($aTab['OPTIONS'])) {
            __AdmSettingsDrawList($module_id, $aTab['OPTIONS']);
            $tabControl->BeginNextTab();
        }
    }
    ?>

    <?php //require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php"); ?>

    <?php $tabControl->Buttons(array('btnApply' => false, 'btnCancel' => false, 'btnSaveAndAdd' => false)); ?>

    <?=bitrix_sessid_post();?>
</form>
<?php $tabControl->End(); ?>
<!-- X FORM TAB -->
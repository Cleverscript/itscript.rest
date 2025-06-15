<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/**
 * @var $this CBitrixComponentTemplate
 * @var CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */

$this->setFrameMode(true);

$APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    '',
    [
        'GRID_ID' => $arResult['GRID_ID'],
        'COLUMNS' => $arResult['COLUMNS'],
        'ROWS' => $arResult['ROWS'],
        'NAV_OBJECT' => $arResult['NAV_OBJECT'],
        'ALLOW_SORT' => $arResult['ALLOW_SORT'],
        "AJAX_MODE" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_HISTORY" => "N",
        "SHOW_ROW_CHECKBOXES" => $arResult['SHOW_ROW_CHECKBOXES'] == 'N',
        "SHOW_SELECTED_COUNTER" => false,
        "SHOW_PAGESIZE" => true,
        "TOTAL_ROWS_COUNT" => $arResult['TOTAL_ROWS_COUNT'],
        'PAGE_SIZES' => [
            ['NAME' => '10', 'VALUE' => '10'],
            ['NAME' => '20', 'VALUE' => '20'],
            ['NAME' => '50', 'VALUE' => '50'],
            ['NAME' => '100', 'VALUE' => '100']
        ],
    ],
    $this->getComponent()
);
?>
<?php if (!empty($arParams['AJAX_LOADER'])) { ?>
    <script>
        BX.addCustomEvent('Grid::beforeRequest', function (gridData, argse) {
            if (argse.gridId != '<?=$arResult['GRID_ID'];?>') {
                return;
            }

            argse.method = 'POST'
            argse.data = <?= \Bitrix\Main\Web\Json::encode($arParams['AJAX_LOADER']['data']) ?>
        });
    </script>
<?php } ?>
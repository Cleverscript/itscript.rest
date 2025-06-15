<?php
use Bitrix\Main\Localization\Loc;
?>

<h2><?=Loc::getMessage('ITSCRIPT_REST_DELETE_TABLES_TITLE');?></h2>

<form action="" method="POST">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
    <input type="hidden" name="uninstall" value="Y">
    <input type="hidden" name="step" value="2">

    <p>
        <label><?=Loc::getMessage('ITSCRIPT_REST_DELETE_YES');?> <input type="radio" name="delete_tables" value="Y"/></label>
    </p>
    <p>
        <label><?=Loc::getMessage('ITSCRIPT_REST_DELETE_NO');?> <input type="radio" name="delete_tables" value="N" checked/></label>
    </p>

    <p>
        <button type="submit" class="adm-btn adm-btn-save">
            <?=Loc::getMessage('ITSCRIPT_REST_DELETE_SUBMIT');?>
        </button>
    </p>
</form>
<?php
$basePath = $_SERVER["DOCUMENT_ROOT"];
$filePath = "modules/itscript.rest/admin/itscript_rest_routes.php";
if(file_exists($basePath . "/bitrix/" . $filePath)) {
    require($basePath . "/bitrix/" . $filePath);
} elseif($basePath . "/local/" . $filePath) {
    require($basePath . "/local/" . $filePath);
}
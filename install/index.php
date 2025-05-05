<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\EventManager;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Localization\Loc;
use Itscript\Rest\Tables\RoutesTable;

Loc::loadMessages(__FILE__);

/**
 * Class itscript_rest
 */

if (class_exists("itscript_rest")) return;

class itscript_rest extends CModule
{
    public $MODULE_ID = "itscript.rest";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    public $MODULE_SORT;
    public $SHOW_SUPER_ADMIN_GROUP_RIGHTS;
    public $MODULE_GROUP_RIGHTS;

    public $eventManager;

    private string $localPath;
    private string $compPath;

    function __construct()
    {
        $arModuleVersion = array();
        include(__DIR__ . "/version.php");

        $this->exclusionAdminFiles = array(
            '..',
            '.'
        );

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage("ITSCRIPT_REST_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("ITSCRIPT_REST_MODULE_DESC");

        $this->PARTNER_NAME = Loc::getMessage("ITSCRIPT_REST_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("ITSCRIPT_REST_PARTNER_URI");

        $this->MODULE_SORT = 1;
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
        $this->MODULE_GROUP_RIGHTS = "Y";

        $this->eventManager = EventManager::getInstance();
        $this->localPath = $_SERVER["DOCUMENT_ROOT"] . '/local';
    }

    public function isVersionD7()
    {
        return CheckVersion(ModuleManager::getVersion('main'), '20.00.00');
    }

    public function GetPath($notDocumentRoot = false)
    {
        if ($notDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
        } else {
            return dirname(__DIR__);
        }
    }

    public static function getModuleId(): string
    {
        return basename(dirname(__DIR__));
    }

    public function getVendor(): string
    {
        $expl = explode('.', $this->MODULE_ID);
        return $expl[0];
    }

    function InstallFiles()
    {
        \CheckDirPath($this->localPath);

        if (!CopyDirFiles(
            $this->GetPath() . '/install/admin',
            $_SERVER["DOCUMENT_ROOT"] . '/bitrix/admin/', true)
        ) {

            return false;
        }

        return true;
    }

    function UnInstallFiles()
    {

    }

    protected function getEventsArray()
    {
        return [
            ['main', 'OnProlog', '\\Itscript\\Rest\\Handlers\\Base', 'init'],
        ];
    }

    function InstallEvents()
    {
        foreach ($this->getEventsArray() as $row)
        {
            list($module, $event_name, $class, $function, $sort) = $row;
            $this->eventManager->RegisterEventHandler($module, $event_name, $this->MODULE_ID, $class, $function, $sort);
        }
        return true;
    }

    function UnInstallEvents()
    {
        foreach ($this->getEventsArray() as $row)
        {
            list($module, $event_name, $class, $function, ) = $row;
            $this->eventManager->UnRegisterEventHandler($module, $event_name, $this->MODULE_ID, $class, $function);
        }
        return true;
    }

    private function getEntities()
    {
        return [
            '\\' . RoutesTable::class
        ];
    }

    public function InstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        $entities = $this->getEntities();

        foreach ($entities as $entity) {
            if (!Application::getConnection($entity::getConnectionName())->isTableExists($entity::getTableName())) {
                Base::getInstance($entity)->createDbTable();
            }
        }

        return true;
    }

    public function UninstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        $connection = Application::getConnection();

        $entities = $this->getEntities();

        foreach ($entities as $entity) {
            if (Application::getConnection($entity::getConnectionName())->isTableExists($entity::getTableName())) {
                $connection->dropTable($entity::getTableName());
            }
        }

        return true;
    }

    function DoInstall()
    {
        global $APPLICATION;

        if (!$this->isVersionD7()) {
            $APPLICATION->ThrowException(Loc::getMessage('ITSCRIPT_REST_INSTALL_ERROR_VERSION'));
            return false;
        }

        ModuleManager::registerModule($this->MODULE_ID);

        if (!$this->InstallFiles()) {
            return false;
        }

        if (!$this->InstallDB()) {
            return false;
        }

        $this->InstallEvents();

        return true;
    }

    function DoUninstall()
    {
        $this->UnInstallEvents();
        $this->UnInstallFiles();
        $this->UninstallDB();

        ModuleManager::unRegisterModule($this->MODULE_ID);

        return true;
    }

    function GetModuleRightList()
    {
        return [
            "reference_id" => ["D", "K", "S", "W"],
            "reference" => [
                "[D] " . Loc::getMessage("ITSCRIPT_REST_RIGHT_DENIED"),
                "[K] " . Loc::getMessage("ITSCRIPT_REST_RIGHT_READ"),
                "[S] " . Loc::getMessage("ITSCRIPT_REST_RIGHT_WRITE_SETTINGS"),
                "[W] " . Loc::getMessage("ITSCRIPT_REST_RIGHT_FULL")
            ]
        ];
    }
}
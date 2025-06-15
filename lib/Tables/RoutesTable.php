<?php

namespace Itscript\Rest\Tables;

use Bitrix\Main\ORM\Event;
use Bitrix\Main\Application;
use Itscript\Rest\Helpers\Config;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\IntegerField;


/**
 * Класс описывающий ORM модель сущности товара
 * который используется в бизнес-процессе "Запрос на закупку"
 */
class RoutesTable extends DataManager
{
    public static function getTableName()
    {
        return 'itscript_rest_routes';
    }

    public static function getMap()
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new StringField('METHOD'))
                ->configureRequired(),

            (new StringField('PATH'))
                ->configureRequired(),

            (new StringField('HANDLER'))
                ->configureRequired(),

            (new StringField('ACTIVE'))
                ->configureRequired()
                ->configureDefaultValue('Y')
        ];
    }

    public static function OnAfterAdd(Event $event) {
        self::clearCache();
    }

    public static function OnAfterUpdate(Event $event) {
        self::clearCache();
    }

    public static function OnAfterDelete(Event $event) {
        self::clearCache();
    }

    private static function clearCache()
    {
        (Application::getInstance()->getTaggedCache())->clearByTag(Config::ROUTES_CACHE_TAG);
        (Application::getInstance()->getTaggedCache())->clearByTag(Config::CMP_GRID_ROUTS_CACHE_TAG);
    }
}

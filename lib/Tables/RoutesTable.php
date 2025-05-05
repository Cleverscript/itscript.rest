<?php

namespace Itscript\Rest\Tables;

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

            (new StringField('ACTIVE'))
                ->configureDefaultValue('N')
                ->configureRequired(),

            (new StringField('METHOD'))
                ->configureRequired(),

            (new StringField('PATH'))
                ->configureRequired(),

            (new StringField('HANDLER'))
                ->configureRequired(),
        ];
    }
}

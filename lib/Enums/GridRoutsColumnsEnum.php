<?php

namespace Itscript\Rest\Enums;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

enum GridRoutsColumnsEnum: string
{
    case ID = 'ID';
    case METHOD = 'METHOD';
    case PATH = 'PATH';
    case HANDLER = 'HANDLER';
    case ACTIVE = 'ACTIVE';

    public static function values(): array {
        return array_column(self::cases(), 'value');
    }

    public static function list(): array {
        return array_combine(
            self::values(),
            array_map(fn($case) => self::match($case), self::cases())
        );
    }

    public static function match($case) {
        return match($case) {
            self::ID => Loc::getMessage('ITSCRIPT_REST_GRID_ROUTS_ID'),
            self::METHOD => Loc::getMessage('ITSCRIPT_REST_GRID_ROUTS_METHOD'),
            self::PATH => Loc::getMessage('ITSCRIPT_REST_GRID_ROUTS_PATH'),
            self::HANDLER => Loc::getMessage('ITSCRIPT_REST_GRID_ROUTS_HANDLER'),
            self::ACTIVE => Loc::getMessage('ITSCRIPT_REST_GRID_ROUTS_ACTIVE'),
        };
    }

    public function text() {
        return self::match($this);
    }
}

<?php

namespace Itscript\Rest\Repositories;

use Bitrix\Main\Data\Cache;
use Itscript\Rest\Enums\CacheTimeEnum;
use Itscript\Rest\Tables\RoutesTable;

class RoutRepository
{
    public static function getRoutes(string $method): array
    {
        $data = [];
        $cache = Cache::createInstance();
        $cacheTime = CacheTimeEnum::HOUR;
        $cacheId = md5(RoutesTable::getTableName() . $method);

        if ($cache->initCache($cacheTime, $cacheId, '/itscript_rest/routes')) {
            $data = $cache->getVars();
        } elseif ($cache->startDataCache()) {
            $query = RoutesTable::query()
                ->setSelect(['METHOD', 'PATH', 'HANDLER'])
                ->where('METHOD', strtoupper($method))
                ->where('ACTIVE', 'Y');

            $data = $query->exec()->fetchAll();

            if (empty($data)) {
                $cache->abortDataCache();
            }

            $cache->endDataCache($data);
        }

        return $data;
    }
}
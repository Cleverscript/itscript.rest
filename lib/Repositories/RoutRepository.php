<?php

namespace Itscript\Rest\Repositories;

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Application;
use Itscript\Rest\Helpers\Config;
use Itscript\Rest\Tables\RoutesTable;
use Itscript\Rest\Enums\CacheTimeEnum;

class RoutRepository
{
    public static function getRoutes(string $method): array
    {
        $data = [];
        $cache = Cache::createInstance();
        $taggedCache = Application::getInstance()->getTaggedCache();
        $cacheTime = CacheTimeEnum::HOUR;
        $cacheId = md5(RoutesTable::getTableName() . $method);

        if ($cache->initCache($cacheTime, $cacheId, Config::ROUTES_CACHE_DIR)) {
            $data = $cache->getVars();
        } elseif ($cache->startDataCache()) {
            $taggedCache->startTagCache(Config::ROUTES_CACHE_DIR);
            $query = RoutesTable::query()
                ->setSelect(['METHOD', 'PATH', 'HANDLER'])
                ->where('METHOD', strtoupper($method))
                ->where('ACTIVE', 'Y');

            $data = $query->exec()->fetchAll();

            if (empty($data)) {
                $taggedCache->abortTagCache();
                $cache->abortDataCache();
            }

            $taggedCache->registerTag(Config::ROUTES_CACHE_TAG);

            $taggedCache->endTagCache();
            $cache->endDataCache($data);
        }

        return $data;
    }
}
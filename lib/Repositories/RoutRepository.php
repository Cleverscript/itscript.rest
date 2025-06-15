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

    public static function getList(array $columns = ['ID'], array $filter = [], int $page = 1, array $order = ['ID' => 'DESC'], int $limit = 10)
    {
        $offset = $limit * ($page-1);

        $query = RoutesTable::query()
            ->setSelect($columns)
            ->addOrder('ID', 'DESC');

        if (!empty($order)) {
            foreach ($order as $key => $value) {
                $query->addOrder($key, $value);
            }
        }

        if (!empty($filter)) {
            foreach ($filter as $field => $value) {
                $query->where($field, $value);
            }
        }

        $query->setLimit($limit);
        $query->setOffset($offset);

        return $query->fetchAll();
    }
}
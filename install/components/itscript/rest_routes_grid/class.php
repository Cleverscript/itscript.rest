<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Application;
use Bitrix\Main\SystemException;
use Itscript\Rest\Helpers\Config;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Itscript\Rest\Tables\RoutesTable;
use Bitrix\Main\ORM\Fields\ExpressionField;
use Itscript\Rest\Enums\GridRoutsColumnsEnum;
use Itscript\Rest\Repositories\RoutRepository;

Loc::loadMessages(__FILE__);

class RoutsGrid extends CBitrixComponent
{
    const GRID_ID = 'rest_routs_grid';
    const MODULE_ID = 'itscript.rest';

    public function onPrepareComponentParams($arParams)
    {
        if (empty($arParams['CACHE_TIME'])) {
            $arParams['CACHE_TIME'] = 86400;
        }

        return $arParams;
    }

	public function executeComponent(): void
	{
        $request = Context::getCurrent()->getRequest();

        try {
            if (!Loader::includeModule(self::MODULE_ID)) {
                throw new SystemException(
                    Loc::getMessage('ITSCRIPT_REST_FAIL_INCLUDE_MODULE', ['#MID#' => self::MODULE_ID])
                );
            }

            if (isset($request['log_list'])) {
                $page = explode('page-', $request['log_list']);
                $page = $page[1];
            } else {
                $page = 1;
            }

            $totalRowsCount = $this->getTotalCount();

            if (!$totalRowsCount) {
                throw new SystemException(
                    Loc::getMessage('ITSCRIPT_REST_NOT_FOUNT')
                );
            }

            // Get grid options
            $gridOptions = new Bitrix\Main\Grid\Options(self::GRID_ID);
            $navParams = $gridOptions->GetNavParams();

            $gridColumns = self::getColumns();

            if (!$gridColumns->isSuccess()) {
                throw new SystemException(implode(', ', $gridColumns->getErrorMessages()));
            }

            $limit = $this->arParams['NUM_PAGE'] == $navParams['nPageSize'] ? $this->arParams['NUM_PAGE'] : $navParams['nPageSize'];

            // Page navigation
            $nav = new PageNavigation('log_list');
            $nav->allowAllRecords(false)->setPageSize($limit)->initFromUri();
            $nav->setRecordCount($totalRowsCount);

            $gridRows = self::getRows($page, $limit);

            if (!$gridRows->isSuccess()) {
                throw new SystemException(implode(', ', $gridRows->getErrorMessages()));
            }

            $this->arResult = [
                'GRID_ID' => self::GRID_ID,
                'COLUMNS' => $gridColumns->getData(),
                'ROWS' => $gridRows->getData(),
                'NAV_OBJECT' => $nav,
                'TOTAL_ROWS_COUNT' => $totalRowsCount,
                'SHOW_ROW_CHECKBOXES' => $this->arParams['SHOW_ROW_CHECKBOXES'],
                'ALLOW_SORT' => true,
            ];

            $this->IncludeComponentTemplate();
        } catch (\Throwable $e) {
            ShowError($e->getMessage());
            echo '<pre>';
            echo $e->getTraceAsString();
            echo '</pre>';
        }
	}

    private function getColumns(): Result
    {
        $result = new Result;
        $columns = [];

        foreach (GridRoutsColumnsEnum::list() as $key => $value) {
            $columns[] = [
                'id' => $key,
                'name' => $value,
                'default' => true
            ];
        }

        return $result->setData($columns);
    }

    public function getTotalCount(): int
    {
        $cache = Cache::createInstance();
        $taggedCache = Application::getInstance()->getTaggedCache();

        $cacheTime = $this->arParams['CACHE_TIME'];
        $cacheId = md5(self::GRID_ID);
        $cachePath = Config::CMP_GRID_ROUTS_CACHE_PATH;
        $myTag = Config::CMP_GRID_ROUTS_CACHE_TAG;

        if ($cache->initCache($cacheTime, $cacheId, $cachePath)) {
            $cnt = $cache->getVars();
        } elseif ($cache->startDataCache()) {
            $taggedCache->startTagCache($cachePath);

            $cnt = RoutesTable::query()
                ->setSelect([new ExpressionField('CNT', 'COUNT(1)')])
                ->where('ACTIVE', 'Y')
                ->exec()
                ->fetch()['CNT'];

            $taggedCache->registerTag($myTag);

            $taggedCache->endTagCache();
            $cache->endDataCache($cnt);
        }

        return $cnt;
    }

	private function getRows(int $page = 1, int $limit = 10): Result
	{
        $result = new Result;
        $data = [];

        $cache = Cache::createInstance();
        $taggedCache = Application::getInstance()->getTaggedCache();

        $cacheTime = $this->arParams['CACHE_TIME'];
        $cacheId = md5(self::GRID_ID . $page . $limit);
        $cachePath = Config::CMP_GRID_ROUTS_CACHE_PATH;
        $myTag = Config::CMP_GRID_ROUTS_CACHE_TAG;

        if ($cache->initCache($cacheTime, $cacheId, $cachePath)) {
            $rows = $cache->getVars();
        } elseif ($cache->startDataCache()) {
            $taggedCache->startTagCache($cachePath);

            $rows = RoutRepository::getList(GridRoutsColumnsEnum::values(), [], $page, ['ID' => 'DESC'], $limit);

            $taggedCache->registerTag($myTag);

            if (empty($rows)) {
                $taggedCache->abortTagCache();
                $cache->abortDataCache();
            }

            $taggedCache->endTagCache();
            $cache->endDataCache($rows);
        }

        if (empty($rows)) {
            return $result->addError(new Error(Loc::getMessage('ITSCRIPT_REST_NOT_FOUNT')));
        }

        foreach ($rows as $row) {
            $data[] = [
                'id' => $row['ID'],
                'columns' => $row,
                'actions' => []
            ];
        }

        return $result->setData($data);
	}
}


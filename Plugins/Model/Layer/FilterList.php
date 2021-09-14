<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_LayeredNavigation
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\LayeredNavigation\Plugins\Model\Layer;

use Lof\LayeredNavigation\Model\Layer\Filter as LofFilter;
use Lof\LayeredNavigation\Model\Config\AdditionalFiltersConfig;

class FilterList
{
    const CONFIG_ENABLED_XML_PATH   = 'layered_navigation/general/stockFilter';
    const CONFIG_POSITION_XML_PATH  = 'bottom';
    const STOCK_FILTER_CLASS        = LofFilter\Stock::class;
    const STOCK_FILTER = "stock";

    protected $additionalFilters
        = [
            self::STOCK_FILTER  => LofFilter\Stock::class
        ];
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;
    /**
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_layer;
    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\CatalogInventory\Model\ResourceModel\Stock\Status
     */
    protected $_stockResource;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var AdditionalFiltersConfig
     */
    private $additionalFiltersConfig;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockResource
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param AdditionalFiltersConfig $additionalFiltersConfig
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockResource,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        AdditionalFiltersConfig $additionalFiltersConfig
    ) {
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->_stockResource = $stockResource;
        $this->_scopeConfig = $scopeConfig;
        $this->additionalFiltersConfig   = $additionalFiltersConfig;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        $outOfStockEnabled = $this->_scopeConfig->isSetFlag(
            \Magento\CatalogInventory\Model\Configuration::XML_PATH_DISPLAY_PRODUCT_STOCK_STATUS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $extensionEnabled = $this->_scopeConfig->isSetFlag(
            self::CONFIG_ENABLED_XML_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $outOfStockEnabled && $extensionEnabled;
    }

    /**
     * @param \Magento\Catalog\Model\Layer\FilterList\Interceptor|\Smile\ElasticsuiteCatalog\Model\Layer\FilterList\Interceptor $filterList
     * @param \Magento\Catalog\Model\Layer $layer
     * @return array
     */
    public function beforeGetFilters(
        $filterList,
        \Magento\Catalog\Model\Layer $layer
    ) {
        $this->_layer = $layer;
        return array($layer);
    }

    /**
     * @param \Magento\Catalog\Model\Layer\FilterList\Interceptor|\Smile\ElasticsuiteCatalog\Model\Layer\FilterList\Interceptor $filterList
     * @param array $filters
     * @return array
     */
    public function afterGetFilters(
        $filterList,
        array $filters
    ) {
        if ($this->isEnabled()) {
            $layer = $this->_layer;
            $collection = $layer->getProductCollection();
            $websiteId = $this->_storeManager->getStore($collection->getStoreId())->getWebsiteId();
            $this->_addStockStatusToSelect($collection->getSelect(), $websiteId);

            $position = $this->getFilterPosition();
            $stockFilter = $this->getStockFilter();
            switch ($position) {
                case \Lof\LayeredNavigation\Model\Config\Source\Position::POSITION_BOTTOM:
                    $filters[] = $this->getStockFilter();
                    break;
                case \Lof\LayeredNavigation\Model\Config\Source\Position::POSITION_TOP:
                    array_unshift($filters, $stockFilter);
                    break;
                case \Lof\LayeredNavigation\Model\Config\Source\Position::POSITION_AFTER_CATEGORY:
                    $processed = [];
                    $stockFilterAdded = false;
                    foreach ($filters as $key => $value) {
                        $processed[] = $value;
                        if ($value instanceof \Magento\Catalog\Model\Layer\Filter\Category || $value instanceof \Magento\CatalogSearch\Model\Layer\Filter\Category) {
                            $processed[] = $stockFilter;
                            $stockFilterAdded = true;
                        }
                    }
                    $filters = $processed;
                    if (!$stockFilterAdded) {
                        array_unshift($filters, $stockFilter);
                    }
                    break;
            }

            if ($filters) {
                $tmpFilters = [];
                $existFilters = [];
                foreach ($filters as $key => $item) {
                    if ($item->getName() && !in_array($item->getName(), $existFilters)) {
                        $existFilters[] = $item->getName();
                        $tmpFilters[$key] = $item;
                    }
                }
                if ($tmpFilters) {
                    $filters = $tmpFilters;
                }
            }
        }
        return $filters;
    }

    /**
     * @return \Lof\LayeredNavigation\Model\Layer\Filter\Stock
     */
    public function getStockFilter()
    {
        $filter = $this->_objectManager->create(
            $this->getStockFilterClass(),
            ['layer' => $this->_layer]
        );
        return $filter;
    }

    /**
     * @return string
     */
    public function getStockFilterClass()
    {
        return self::STOCK_FILTER_CLASS;
    }

    /**
     * @param \Zend_Db_Select $select
     * @param $websiteId
     * @return $this
     */
    protected function _addStockStatusToSelect(\Zend_Db_Select $select, $websiteId)
    {
        $from = $select->getPart(\Zend_Db_Select::FROM);
        $select->reset(\Zend_Db_Select::ORDER);
        $select->reset(\Zend_Db_Select::LIMIT_COUNT);
        $select->reset(\Zend_Db_Select::LIMIT_OFFSET);
        if (!isset($from['stock_status_idx'])) {
            $joinCondition = $this->_stockResource->getConnection()->quoteInto(
                'e.entity_id = stock_status_idx.product_id AND stock_status_idx.stock_id = ?',
                \Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID
            );
            $select->join(
                [
                    'stock_status_idx' => $this->_stockResource->getMainTable()
                ],
                $joinCondition,
                []
            );
        }

        return $this;
    }

    /**
     * @param array $filters
     * @param array $additionalFilters
     *
     * @return array
     */
    private function applyFilterPosition($filters, $additionalFilters = [])
    {
        if (!$additionalFilters) {
            return $filters;
        }

        foreach ($additionalFilters as $data) {
            foreach ($data as $position => $additionalFilter) {
                if (isset($filters[$position]) && $position != 0) {
                    $firstFilterPart  = array_slice($filters, 0, $position);
                    $secondFilterPart = array_slice($filters, $position);
                    $filters    = array_merge($firstFilterPart, [$additionalFilter], $secondFilterPart);
                } elseif ($position == 0) {
                    array_unshift($filters, $additionalFilter);
                } else {
                    $filters = array_merge($filters, [$additionalFilter]);
                }
            }
        }

        return $filters;
    }

    /**
     * @param \Magento\Catalog\Model\Layer $layer
     *
     * @return AbstractFilter[]
     */
    private function getAdditionalFilters($layer)
    {
        $additionalFilters = [];
        $storeId           = $this->_storeManager->getStore()->getStoreId();

        foreach ($this->additionalFilters as $filter => $class) {
            if ($this->additionalFiltersConfig->isFilterEnabled($filter, $storeId)) {
                $position            = $this->additionalFiltersConfig->getFilterPosition($filter, $storeId);
                $additionalFilters[] = [
                    $position => $this->_objectManager->create($class, ['layer' => $layer]),
                ];
            }
        }
        return $additionalFilters;
    }

    /**
     * get filter position
     * 
     * @return string
     */
    public function getFilterPosition()
    {
        return self::CONFIG_POSITION_XML_PATH;
    }
}

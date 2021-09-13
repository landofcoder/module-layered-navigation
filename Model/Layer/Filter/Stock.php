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

namespace Lof\LayeredNavigation\Model\Layer\Filter;

class Stock extends \Magento\Catalog\Model\Layer\Filter\AbstractFilter
{
    const IN_STOCK_COLLECTION_FLAG = 'lof_stock_filter_applied';
    const CONFIG_FILTER_LABEL_PATH = 'In stock';
    const CONFIG_URL_PARAM_PATH    = 'in-stock';
    protected $_activeFilter = false;
    protected $_requestVar = 'in-stock';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var \Magento\CatalogInventory\Model\ResourceModel\Stock\Status
     */
    protected $_stockResource;

    /**
     * Stock constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockResource
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockResource,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        array $data = []
    ) {
        $this->_stockResource = $stockResource;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $data);
        $this->_requestVar = self::CONFIG_URL_PARAM_PATH;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $filter = $request->getParam($this->getRequestVar(), null);
        if (is_null($filter)) {
            return $this;
        }
        $attributeValue = explode(',', $filter);
        $this->_activeFilter = true;

        if (strpos($filter, ',') == false) {
            $collection = $this->getLayer()->getProductCollection();
            $collection->getSelect()->where('stock_status_idx.stock_status = ?', $filter);
        }

        $state = $this->getLayer()->getState();
        foreach ($attributeValue as $value) {

            $state->addFilter(
                $this->_createItem($this->getLabel($value), $value)
            );
        }
        return $this;
    }

    /**
     * Get filter name
     *
     * @return string
     */
    public function getName()
    {
        return self::CONFIG_FILTER_LABEL_PATH;
    }

    /**
     * Get data array for building status filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $data = [];
        foreach ($this->getStatuses() as $status) {
            $data[] = [
                'label' => $this->getLabel($status),
                'value' => $status,
                'count' => $this->getProductsCount($status)
            ];
        }
        return $data;
    }

    /**
     * get available statuses
     * @return array
     */
    public function getStatuses()
    {
        return [
            \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK,
            \Magento\CatalogInventory\Model\Stock::STOCK_OUT_OF_STOCK
        ];
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        return [
            \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK => __('In Stock'),
            \Magento\CatalogInventory\Model\Stock::STOCK_OUT_OF_STOCK => __('Out of stock'),
        ];
    }

    /**
     * @param $value
     * @return string
     */
    public function getLabel($value)
    {
        $labels = $this->getLabels();
        if (isset($labels[$value])) {
            return $labels[$value];
        }
        return '';
    }



    /**
     * @param $value
     * @return string
     */

    public function getProductsCount($value)
    {
        $collection = $this->getLayer()->getProductCollection();
        if ($collection->getCatalogPreparedSelect() !== null) {
            $select = clone $collection->getCatalogPreparedSelect();
        } else {
            $select = clone $collection->getSelect();
            $select = clone $collection->getProductCountSelect();
        }

        // reset columns, order and limitation conditions
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        $select->reset(\Magento\Framework\DB\Select::ORDER);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);

        $wherePart = $select->getPart(\Magento\Framework\DB\Select::WHERE);
        //remove e.entity_id part of where part
        foreach($wherePart as $id => $where) {
            if (strpos($where, 'e.entity_id IN') >= 0 || strpos($where, 'e.entity_id in') >= 0) {
                unset($wherePart[$id]);
            }
        }
        $select->setPart(\Magento\Framework\DB\Select::WHERE, $wherePart);
        $select->where('stock_status_idx.stock_status = ?', $value);
        $select->columns(
            [
                'count' => new \Zend_Db_Expr("COUNT(e.entity_id)")
            ]
        );

        return $collection->getConnection()->fetchOne($select);
    }
    
}

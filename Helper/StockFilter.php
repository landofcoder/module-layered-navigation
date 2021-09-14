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


namespace Lof\LayeredNavigation\Helper;

use Magento\Catalog\Model\Product\Action as ProductAction;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResourceConnection;
use Lof\LayeredNavigation\Setup\InstallData;

class StockFilter extends AbstractHelper
{
    protected $messageManager;
    private $productCollection;
    private $productAction;
    private $storeManager;
    protected $resourceConnection;

    public function __construct(
        Context $context,
        CollectionFactory $collection,
        ProductAction $action,
        StoreManagerInterface $storeManager,
        ManagerInterface $messageManager,
        ResourceConnection $resourceConnection
    )
    {
        $this->productCollection = $collection;
        $this->productAction = $action;
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }

    /**
     * @param int|string|null
     * @return void
     */
    public function setAttributeData($value)
    {
        try {
            $collection = $this->productCollection->create()->addAttributeToSelect('*');
            $collection->addAttributeToFilter(InstallData::ATTRIBUTE_CODE, 
                [
                    ['eq' => 1],
                    ['eq' => 0]
                ]
            );
            $select = clone $collection->getSelect();
            $select->reset(\Zend_Db_Select::COLUMNS);
            $select->reset(\Zend_Db_Select::ORDER);
            $select->reset(\Zend_Db_Select::LIMIT_COUNT);
            $select->reset(\Zend_Db_Select::LIMIT_OFFSET);

            $connection = $this->resourceConnection->getConnection();
            $table = $connection->getTableName('catalog_product_entity_int');
            // RAW select query
            // TODO: Convert raw query to to ORM Query
            // phpcs:disable Magento2.SQL.RawQuery.FoundRawSql
            $query = "SELECT t.entity_id FROM " . $table . " t
            WHERE t.entity_id NOT IN (SELECT e.entity_id ".$select.") GROUP BY t.entity_id LIMIT 0, 1000";

            $allIds = $connection->fetchAll($query);
            $storeId = $this->storeManager->getStore()->getId();
            
            $ids = [];
            $i = 0;
            foreach ($allIds as $item) {
                $ids[$i] = $item['entity_id'];
                $i++;
            }
            $this->productAction->updateAttributes($ids, [InstallData::ATTRIBUTE_CODE => $value], $storeId);

        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
    }
}
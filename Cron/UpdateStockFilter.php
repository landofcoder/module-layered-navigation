<?php
namespace Lof\LayeredNavigation\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Lof\LayeredNavigation\Setup\InstallData;

class UpdateStockFilter
{

    protected $stockResource;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ResourceConnection $resourceConnection
     * @param \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockResource
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ResourceConnection $resourceConnection,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockResource
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->resourceConnection = $resourceConnection;
        $this->stockResource = $stockResource;
    }
    /**
     * Executes Cronjob for updating 'stock_filter' parameter
     */
    public function execute()
    {
        if ($this->scopeConfig->getValue('layered_navigation/cronjobs/is_enabled') == 1) {
            $connection = $this->resourceConnection->getConnection();
            $table = $connection->getTableName('catalog_product_entity_int');
            // Update query
            $query = "UPDATE " . $table . " t
            JOIN cataloginventory_stock_status a ON a.product_id = t.entity_id
            JOIN eav_attribute ap ON ap.attribute_id = t.attribute_id
            SET value = stock_status WHERE attribute_code = '".InstallData::ATTRIBUTE_CODE."'";
            $connection->query($query);

            $instockProductIds = $this->getProductsWithStockFilter(\Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK);
            $outstockProductIds = $this->getProductsWithStockFilter(\Magento\CatalogInventory\Model\Stock::STOCK_OUT_OF_STOCK);
            // Update query
            if ($outstockProductIds) {
                $outstockQuery = "UPDATE " . $table . " t
                JOIN eav_attribute ap ON ap.attribute_id = t.attribute_id
                SET value = ".\Lof\LayeredNavigation\Model\Config\Source\Options::OUTSTOCK_ID." WHERE attribute_code = '".InstallData::ATTRIBUTE_CODE."' and t.entity_id IN (?)";
                $connection->query($outstockQuery, implode(",", $outstockProductIds));
            }
            if ($instockProductIds) {
                $instockQuery = "UPDATE " . $table . " t
                JOIN eav_attribute ap ON ap.attribute_id = t.attribute_id
                SET value = ".\Lof\LayeredNavigation\Model\Config\Source\Options::INSTOCK_ID." WHERE attribute_code = '".InstallData::ATTRIBUTE_CODE."' and t.entity_id IN (?)";
                $connection->query($instockQuery, implode(",", $instockProductIds));
            }
            
        }
        return $this;
    }

    /**
     * @param $value
     * @return string
     */

    public function getProductsWithStockFilter($value)
    {
        $connection =  $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('catalog_product_entity');
        $select = $connection->select()->from(
            ['e' => $tableName]
            )->where('1 = ?', 1);
        
        $joinCondition = $this->stockResource->getConnection()->quoteInto(
            'e.entity_id = stock_status_idx.product_id AND stock_status_idx.stock_id = ?',
            \Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID
        );
        $select->join(
            [
                'stock_status_idx' => $this->stockResource->getMainTable()
            ],
            $joinCondition,
            []
        );

        // reset columns, order and limitation conditions
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        $select->reset(\Magento\Framework\DB\Select::ORDER);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $select->where('stock_status_idx.stock_status = ?', $value);
        $select->columns(
            [
                'entity_id'
            ]
        );

        return $connection->fetchCol($select);
    }
}

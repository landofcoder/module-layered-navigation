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
            return $this->processStockForProduct();
        }
        return $this;
    }

    /**
     * Executes Cronjob for updating 'stock_filter' parameter
     * 
     * @param int|null $product_id
     * @return $this
     */
    public function processStockForProduct($product_id = null)
    {
        if ($this->scopeConfig->getValue('layered_navigation/general/stockFilter') == 1) {
            $connection = $this->resourceConnection->getConnection();
            $table = $connection->getTableName('catalog_product_entity_int');
            // RAW update query
            // TODO: Convert raw query to to ORM Query
            // phpcs:disable Magento2.SQL.RawQuery.FoundRawSql
            $query = "UPDATE " . $table . " t
            JOIN cataloginventory_stock_status a ON a.product_id = t.entity_id
            JOIN eav_attribute ap ON ap.attribute_id = t.attribute_id
            SET value = stock_status WHERE attribute_code = '".InstallData::ATTRIBUTE_CODE."'";
            if ($product_id) {
                $query .= " AND t.entity_id = ".(int)$product_id;
            }
            $connection->query($query);
        }
        return $this;
    }
}

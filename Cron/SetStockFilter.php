<?php
namespace Lof\LayeredNavigation\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Lof\LayeredNavigation\Setup\InstallData;
use Lof\LayeredNavigation\Helper\StockFilter;
use Lof\LayeredNavigation\Model\Config\Source\Options;

class SetStockFilter
{

    protected $stockResource;

    /**
     * @var StockFilter
     */
    protected $stockFilterHelper;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ResourceConnection $resourceConnection
     * @param \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockResource
     * @param StockFilter $stockFilterHelper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ResourceConnection $resourceConnection,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockResource,
        StockFilter $stockFilterHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->resourceConnection = $resourceConnection;
        $this->stockResource = $stockResource;
        $this->stockFilterHelper = $stockFilterHelper;
    }

    /**
     * Executes Cronjob for updating 'stock_filter' parameter
     */
    public function execute()
    {
        if ($this->scopeConfig->getValue('layered_navigation/cronjobs/is_enabled') == 1) {
            $this->stockFilterHelper->setAttributeData(Options::INSTOCK_ID);
        }
        return $this;
    }

}

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
use Magento\Store\Model\StoreManagerInterface;
use Lof\LayeredNavigation\Setup\InstallData;

class StockFilter extends AbstractHelper
{
    protected $messageManager;
    private $productCollection;
    private $productAction;
    private $storeManager;

    public function __construct(
        Context $context,
        CollectionFactory $collection,
        ProductAction $action,
        StoreManagerInterface $storeManager
    )
    {
        $this->productCollection = $collection;
        $this->productAction = $action;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param int|string|null
     * @return void
     */
    public function setAttributeData($value)
    {
        try {
            $collection = $this->productCollection->create()->addFieldToFilter('*');
            $collection->addFieldToFilter(
                [
                    [
                        'attribute'=> InstallData::ATTRIBUTE_CODE, 'notnull' => true
                    ]
                ]
            );

            $storeId = $this->storeManager->getStore()->getId();
            $ids = [];
            $i = 0;
            foreach ($collection as $item) {
                $ids[$i] = $item->getEntityId();
                $i++;
            }
            $this->productAction->updateAttributes($ids, [InstallData::ATTRIBUTE_CODE => $value], $storeId);

        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
    }
}
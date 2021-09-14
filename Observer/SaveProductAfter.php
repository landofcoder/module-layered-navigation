<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_LayeredNavigation
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\LayeredNavigation\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveProductAfter implements ObserverInterface
{
    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogData;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Lof\LayeredNavigation\Cron\UpdateStockFilter
     */
    protected $_updateStockFilter;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\App\ResourceConnection  $resource
     * @param \Magento\Framework\Registry  $coreRegistry
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Lof\LayeredNavigation\Cron\UpdateStockFilter $updateStockFilter
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\RequestInterface $request,
        \Lof\LayeredNavigation\Cron\UpdateStockFilter $updateStockFilter
        )
    {
        $this->_resource = $resource;
        $this->_coreRegistry = $coreRegistry;
        $this->_request = $request;
        $this->_updateStockFilter = $updateStockFilter;
    }

    /**
     * Checking whether the using static urls in WYSIWYG allowed event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $_product = $observer->getProduct();
        $productId = $_product->getId();
        $this->_updateStockFilter->processStockForProduct($productId);
    }
}

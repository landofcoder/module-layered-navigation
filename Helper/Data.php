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

use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data  extends AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Data constructor.
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager
    )
    {
        $this->objectManager   = $objectManager;
        $this->storeManager    = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return CurrentUrl | string
     */
    public function getCurrentUrl(){
        $model=$this->objectManager->get('Magento\Framework\UrlInterface');
        return $model->getCurrentUrl();
    }

	public function isEnabled($storeId = null)
	{
		return $this->getConfigValue('layered_navigation/general/enable', $storeId);
	}

    public function getStoreConfig($storePath){
        $StoreConfig =  $this->scopeConfig->getValue($storePath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $StoreConfig;
    }

    public function initProductLayout(ResultPage $resultPage,$layout_id)
    {
        $post_list_layout = $this->getStoreConfig($layout_id);
        $pageConfig = $resultPage->getConfig();
        $pageConfig->setPageLayout($post_list_layout);
        return $this;
    }

    public function prepareAndRender(ResultPage $resultPage, $controller)
    {
        $this->initProductLayout($resultPage,'layered_navigation/general/typelayer');
        return $this;
    }
}

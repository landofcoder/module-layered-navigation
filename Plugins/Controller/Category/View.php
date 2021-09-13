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
namespace Lof\LayeredNavigation\Plugins\Controller\Category;

class View
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
	protected $_jsonHelper;

    /**
     * @var \Lof\LayeredNavigation\Helper\Data
     */
	protected $_helperFunction;

    /**
     * View constructor.
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Lof\LayeredNavigation\Helper\Data $helperFunction
     */
	public function __construct(
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		\Lof\LayeredNavigation\Helper\Data $helperFunction
	){
		$this->_jsonHelper = $jsonHelper;
		$this->_helperFunction = $helperFunction;
	}

	/**
	 * after excecute
	 * 
	 * @param \Magento\Catalog\Controller\Category\View $action
	 * @param \Magento\Framework\View\Result\Page|mixed $page
	 * @return \Magento\Framework\View\Result\Page|mixed
	 */
    public function afterExecute(\Magento\Catalog\Controller\Category\View $action, $page)
	{
		$catalogAjax = $action->getRequest()->getParam('catalogajax');
		$ajaxscroll = $action->getRequest()->getParam('ajaxscroll');
		if ($this->_helperFunction->isEnabled() && ($catalogAjax || $action->getRequest()->getParam('isAjax')) && !$ajaxscroll) {
			$navigation = $page->getLayout()->getBlock('catalog.leftnav');
			$products = $page->getLayout()->getBlock('category.products');
			$result = ['products' => $products->toHtml(), 'navigation' => $navigation->toHtml()];
			$action->getResponse()->representJson($this->_jsonHelper->jsonEncode($result));
		} else {
			if ($page && $page instanceof \Magento\Framework\View\Result\Page) {
				$this->_helperFunction->prepareAndRender($page,$this);
			}
		    
			return $page;
		}
    }
}

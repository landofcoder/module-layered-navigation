<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
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
    public function afterExecute(\Magento\Catalog\Controller\Category\View $action, $page)
	{
		$catalogAjax = $action->getRequest()->getParam('catalogajax');
		$ajaxscroll = $action->getRequest()->getParam('ajaxscroll');
		if($this->_helperFunction->isEnabled() && ($catalogAjax || $action->getRequest()->getParam('isAjax')) && !$ajaxscroll){
			$navigation = $page->getLayout()->getBlock('catalog.leftnav');
			$products = $page->getLayout()->getBlock('category.products');
			$result = ['products' => $products->toHtml(), 'navigation' => $navigation->toHtml()];
			$action->getResponse()->representJson($this->_jsonHelper->jsonEncode($result));
		} else {
			if($page && $page instanceof \Magento\Framework\View\Result\Page){
				$this->_helperFunction->prepareAndRender($page,$this);
			}
		    
			return $page;
		}
    }
}

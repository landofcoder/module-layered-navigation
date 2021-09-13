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

namespace Lof\LayeredNavigation\Plugins\Model\Layer\Filter;

class Item
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
	protected $_url;

    /**
     * @var \Magento\Theme\Block\Html\Pager
     */
	protected $_htmlPagerBlock;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
	protected $_request;

    /**
     * @var \Lof\LayeredNavigation\Helper\Data
     */
	protected $_helperFunction;

    /**
     * Item constructor.
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Theme\Block\Html\Pager $htmlPagerBlock
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Lof\LayeredNavigation\Helper\Data $helperFunction
     */
	public function __construct(
		\Magento\Framework\UrlInterface $url,
		\Magento\Theme\Block\Html\Pager $htmlPagerBlock,
		\Magento\Framework\App\RequestInterface $request,
		\Lof\LayeredNavigation\Helper\Data $helperFunction
	) {
		$this->_url = $url;
		$this->_htmlPagerBlock = $htmlPagerBlock;
		$this->_request = $request;
		$this->_helperFunction = $helperFunction;
	}

    public function aroundGetUrl(\Magento\Catalog\Model\Layer\Filter\Item $item, $proceed)
    {
		if (!$this->_helperFunction->isEnabled()) {
			return $proceed();
		}

		$enable_price_slider = $this->_helperFunction->getStoreConfig("layered_navigation/general/enable_price_slider");

		$value = array();
		$requestVar = $item->getFilter()->getRequestVar();
		if ($requestValue = $this->_request->getParam($requestVar)) {
			$value = explode(',', $requestValue);
		}
		$value[] = $item->getValue();

		if ($requestVar == 'price') {
			$value = ["{price_start}-{price_end}"];
			if (!$enable_price_slider) {
				return $proceed();
			}
		}

        $query = [
			$item->getFilter()->getRequestVar() => implode(',', $value),
            // exclude current page from urls
			$this->_htmlPagerBlock->getPageVarName() => null,
        ];
        return $this->_url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

    public function aroundGetRemoveUrl(\Magento\Catalog\Model\Layer\Filter\Item $item, $proceed)
    {
		if (!$this->_helperFunction->isEnabled()) {
			return $proceed();
		}

		$value = array();
		$requestVar = $item->getFilter()->getRequestVar();
		if ($requestValue = $this->_request->getParam($requestVar)) {
			$value = explode(',', $requestValue);
		}

		if (in_array($item->getValue(), $value)) {
			$value = array_diff($value, array($item->getValue()));
		}

		if ($requestVar == 'price') {
			$value = [];
		}

        $query = [$requestVar => count($value) ? implode(',', $value) : $item->getFilter()->getResetValue()];
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $query;
        $params['_escape'] = true;
        return $this->_url->getUrl('*/*/*', $params);
    }
}

<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\LayeredNavigation\Model\Layer\Filter;

use Magento\CatalogSearch\Model\Layer\Filter\Price as AbstractFilter;

/**
 * Layer price filter based on Search API
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Price extends AbstractFilter
{
	/**
	 * @var \Magento\Catalog\Model\Layer\Filter\DataProvider\Price
	 */
	private $dataProvider;

	/**
	 * @var \Magento\Framework\Pricing\PriceCurrencyInterface
	 */
	private $priceCurrency;

    /**
     * @var \Lof\LayeredNavigation\Helper\Data
     */
	protected $_helperFunction;

    /**
     * Price constructor.
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory
     * @param \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory
     * @param \Lof\LayeredNavigation\Helper\Data $helperFunction
     * @param array $data
     */
	public function __construct(
		\Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\Layer $layer,
		\Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
		\Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm,
		\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
		\Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory,
		\Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory,
		\Lof\LayeredNavigation\Helper\Data $helperFunction,
		array $data = []
	)
	{
		parent::__construct(
			$filterItemFactory,
			$storeManager,
			$layer,
			$itemDataBuilder,
			$resource,
			$customerSession,
			$priceAlgorithm,
			$priceCurrency,
			$algorithmFactory,
			$dataProviderFactory,
			$data
		);

		$this->priceCurrency = $priceCurrency;
		$this->dataProvider  = $dataProviderFactory->create(['layer' => $this->getLayer()]);
		$this->_helperFunction = $helperFunction;
	}

	/**
	 * Apply price range filter
	 *
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @return $this
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function apply(\Magento\Framework\App\RequestInterface $request)
	{
		if (!$this->_helperFunction->isEnabled()) {
			return parent::apply($request);
		}
		/**
		 * Filter must be string: $fromPrice-$toPrice
		 */
		$filter = $request->getParam($this->getRequestVar());
		if (!$filter || is_array($filter)) {
			$this->filterValue = false;
			return $this;
		}

		$filterParams = explode(',', $filter);
		$filter       = $this->dataProvider->validateFilter($filterParams[0]);
		if (!$filter) {
			$this->filterValue = false;
			return $this;
		}

		$this->dataProvider->setInterval($filter);
		$priorFilters = $this->dataProvider->getPriorFilters($filterParams);
		if ($priorFilters) {
			$this->dataProvider->setPriorIntervals($priorFilters);
		}

		list($from, $to) = $filter;

		$this->getLayer()->getProductCollection()->addFieldToFilter(
            'price',
            ['from' => $from, 'to' =>  empty($to) || $from == $to ? $to : $to - self::PRICE_DELTA]
        );

		$this->getLayer()->getState()->addFilter(
			$this->_createItem($this->_renderRangeLabel(empty($from) ? 0 : $from, $to), $filter)
		);
		

		return $this;
	}

	/**
     * Prepare text of range label
     *
     * @param float|string $fromPrice
     * @param float|string $toPrice
     * @param boolean $isLast
     * @return float|\Magento\Framework\Phrase
     */
    protected function _renderRangeLabel($fromPrice, $toPrice, $isLast = false)
    {
		if (!$this->_helperFunction->isEnabled()) {
			return parent::_renderRangeLabel($fromPrice, $toPrice);
		}
		$fromPrice = empty($fromPrice) ? 0 : $fromPrice * $this->getCurrencyRate();
        $toPrice = empty($toPrice) ? $toPrice : $toPrice * $this->getCurrencyRate();

		$formattedFromPrice = $this->priceCurrency->format($fromPrice);
		if ($isLast) {
            return __('%1 and above', $formattedFromPrice);
        } elseif ($fromPrice == $toPrice && $this->dataProvider->getOnePriceIntervalValue()) {
            return $formattedFromPrice;
        } else {
            if ($fromPrice != $toPrice) {
                $toPrice -= .01;
            }

            return __('%1 - %2', $formattedFromPrice, $this->priceCurrency->format($toPrice));
        }
	}

	/**
	 * Get data array for building attribute filter items
	 *
	 * @return array
	 *
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	protected function _getItemsData()
	{
		if (!$this->_helperFunction->isEnabled()) {
			return parent::_getItemsData();
		}

		$data = [[
			'label' => '0-100',
			'value' => '0-100',
			'count' => 1,
			'from'  => '0',
			'to'    => '100',
		]];

		return $data;
	}
}

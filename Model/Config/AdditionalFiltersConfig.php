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

namespace Lof\LayeredNavigation\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;

class AdditionalFiltersConfig
{
    const NEW_FILTER                      = 'new_products';
    const ON_SALE_FILTER                  = 'on_sale';
    const STOCK_FILTER                    = 'stock_status';
    const IN_STOCK_FILTER                 = 1;
    const OUT_OF_STOCK_FILTER             = 2;
    const RATING_FILTER                   = 'rating_summary';
    const NEW_FILTER_FRONT_PARAM          = 'new_products';
    const ON_SALE_FILTER_FRONT_PARAM      = 'on_sale';
    const STOCK_FILTER_FRONT_PARAM        = 'stock';
    const RATING_FILTER_FRONT_PARAM       = 'rating';
    const NEW_FILTER_DEFAULT_LABEL        = 'New';
    const ON_SALE_FILTER_DEFAULT_LABEL    = 'Sale';
    const STOCK_FILTER_DEFAULT_LABEL      = 'Stock';
    const RATING_FILTER_DEFAULT_LABEL     = 'Rating';
    const RATING_FILTER_DATA              = 'm__rating_filter_data';
    const RATING_DATA
                                          = [
            5 => 100,
            4 => 80,
            3 => 60,
            2 => 40,
            1 => 20,
        ];
    const STOCK_FILTER_IN_STOCK_LABEL     = 'instock';
    const STOCK_FILTER_OUT_OF_STOCK_LABEL = 'outofstock';
    const RATING_FILTER_ONE_LABEL         = 'rating1';
    const RATING_FILTER_TWO_LABEL         = 'rating2';
    const RATING_FILTER_THREE_LABEL       = 'rating3';
    const RATING_FILTER_FOUR_LABEL        = 'rating4';
    const RATING_FILTER_FIVE_LABEL        = 'rating5';

    /**
     * @inheritdoc
     */
    public function isFilterEnabled($filter, $store = null)
    {
        $method = 'is' . $this->transformToMethod($filter) . 'FilterEnabled';
        if (!method_exists($this, $method)) {
            throw new LocalizedException(__('Filter type "%1" does not exist', $filter));
        }

        return $this->{$method}($store);
    }

    public function getFilterPosition($filter, $store = null)
    {
        $method = 'get' . $this->transformToMethod($filter) . 'FilterPosition';
        if (!method_exists($this, $method)) {
            throw new LocalizedException(__('Filter type "%1" does not exist', $filter));
        }

        return $this->{$method}($store);
    }

    /**
     * Transform given str to Upper Camel Case compatible string for use in method.
     *
     * @param $str
     *
     * @return string
     */
    private function transformToMethod($str)
    {
        return str_replace('_', '', ucwords($str, '_'));
    }

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    // New Filter

    /**
     * {@inheritdoc}
     */
    public function isNewFilterEnabled($store = null)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewFilterLabel($store = null)
    {
        return __("New");
    }

    /**
     * {@inheritdoc}
     */
    public function getNewFilterPosition($store = null)
    {
        return 100;
    }

    // On Sale Filter

    /**
     * {@inheritdoc}
     */
    public function isOnSaleFilterEnabled($store = null)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getOnSaleFilterLabel($store = null)
    {
        return __("Sale");
    }

    /**
     * {@inheritdoc}
     */
    public function getOnSaleFilterPosition($store = null)
    {
        return 100;
    }

    // Stock Filter

    /**
     * {@inheritdoc}
     */
    public function isStockFilterEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            'layered_navigation/general/stockFilter',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getStockFilterLabel($store = null)
    {
        return __("Stock");
    }

    /**
     * {@inheritdoc}
     */
    public function getInStockFilterLabel($store = null)
    {
        return __("In Stock");
    }

    /**
     * {@inheritdoc}
     */
    public function getOutOfStockFilterLabel($store = null)
    {
        return __("Out of Stock");
    }

    /**
     * {@inheritdoc}
     */
    public function getStockFilterPosition($store = null)
    {
        return 100;
    }

    // Rating Filter

    /**
     * {@inheritdoc}
     */
    public function isRatingFilterEnabled($store = null)
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getRatingFilterLabel($store = null)
    {
        return __("Rating");
    }

    /**
     * {@inheritdoc}
     */
    public function getRatingFilterPosition($store = null)
    {
        return 100;
    }
}

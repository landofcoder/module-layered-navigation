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


namespace Lof\LayeredNavigation\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const AJAX_SUFFIX                    = 'mAjax';
    const AJAX_PRODUCT_LIST_WRAPPER_ID   = 'lof-navigation-product-list-wrapper';
    const AJAX_STATE_WRAPPER_ID          = 'lof-navigation-state-wrapper';
    const AJAX_STATE_WRAPPER_CLASS       = 'lof-navigation-state';
    const AJAX_STATE_WRAPPER_INPUT_CLASS = 'lof-navigation-state-input';
    const AJAX_SWATCH_WRAPPER_CLASS      = 'lof-navigation-swatch';

    const NAV_IMAGE_REG_PRODUCT_DATA = 'lof-navigation-register-product-data';

    const NAV_REPLACER_TAG = '<div id="lof-navigation-replacer"></div>'; //use for filter opener

    const IS_CATALOG_SEARCH     = 'catalogsearch';
    const IS_PRICE_SLIDER_ADDED = 'lof__is_price_slider_added';

    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function isSeoFiltersEnabled()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isAjaxEnabled()
    {
        return 1;
    }

    /**
     * @return bool
     */
    public function isShowNestedCategories()
    {
        return 1;
    }

    /**
     * @return bool
     */
    public function isMultiselectEnabled()
    {
        return 1;
    }

    /**
     * @return bool
     */
    public function getMultiselectDisplayOptions()
    {
        return 1;
    }

    /**
     * @return bool
     */
    public function getDisplayOptionsBackgroundColor()
    {
        return "";
    }

    /**
     * @return bool
     */
    public function getDisplayOptionsBorderColor()
    {
        return "";
    }

    /**
     * @return bool
     */
    public function getDisplayOptionsCheckedLabelColor()
    {
        return "";
    }

    /**
     * @return bool
     */
    public function isShowOpenedFilters()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isCorrectElasticFilterCount()
    {
        return false;
    }

}

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

use Magento\Framework\App\ObjectManager;

trait ConfigTrait
{
    /**
     * @return bool
     */
    public function isAjaxEnabled()
    {
        return true;
    }

    /**
     * @return int
     */
    static public function isMultiselectEnabled()
    {
        return 1;
    }


    /**
     * Is allowed to process request.
     *
     * @param \Magento\Framework\App\Request\Http|\Magento\Framework\App\RequestInterface $request
     *
     * @return bool
     */
    protected function isAllowed($request)
    {
        return true;
    }

    /**
     * Is request triggered by external modules.
     *
     * @param \Magento\Framework\App\Request\Http|\Magento\Framework\App\RequestInterface $request
     *
     * @return bool
     */
    protected function isExternalRequest($request)
    {
        $externalParams = ['ajaxscroll', 'is_scroll'];
        $params         = $request->getParams();

        foreach ($externalParams as $param) {
            if (array_key_exists($param, $params)) {
                return true;
            }
        }

        return false;
    }

    static public function isShowNestedCategories($store = null)
    {
        return true;
    }
}

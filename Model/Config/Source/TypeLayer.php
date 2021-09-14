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

namespace Lof\LayeredNavigation\Model\Config\Source;

class TypeLayer implements \Magento\Framework\Option\ArrayInterface{

    public function toOptionArray()
    {
        return [
            ['value' => '2columns-left', 'label' => __('Vertical')], 
            ['value' => '1column', 'label' => __('Horizontal')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['2columns-left' => __('Vertical'), '1column' => __('Horizontal')];
    }
}
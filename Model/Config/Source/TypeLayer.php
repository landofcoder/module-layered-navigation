<?php

namespace Lof\LayeredNavigation\Model\Config\Source;

class TypeLayer implements \Magento\Framework\Option\ArrayInterface{

    public function toOptionArray()
    {
        return [['value' => '2columns-left', 'label' => __('Vertical')], ['value' => '1column', 'label' => __('Horizontal')]];
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
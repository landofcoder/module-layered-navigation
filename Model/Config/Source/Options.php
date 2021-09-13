<?php

namespace Lof\LayeredNavigation\Model\Config\Source;

class Options extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const OUTSTOCK_ID = 0;
    const INSTOCK_ID = 1;
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
                ['label' => __('No'), 'value' => ''],
                ['label' => __('Out of Stock'), 'value' => 0],
                ['label' => __('In Stock'), 'value' => 1]
        ];
        return $this->_options;
    }
}

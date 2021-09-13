<?php

namespace Lof\LayeredNavigation\Model\Config\Source;

class Options extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
                ['label' => __('No'), 'value' => ''],
                ['label' => __('Out Stock'), 'value' => 0],
                ['label' => __('In Stock'), 'value' => 1]
        ];
        return $this->_options;
    }
}

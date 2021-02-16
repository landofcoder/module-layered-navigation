<?php

namespace Lof\LayeredNavigation\Model\Config\Source;

class Position
{
    const POSITION_TOP = 'top';
    const POSITION_BOTTOM = 'bottom';
    const POSITION_AFTER_CATEGORY = 'after_category';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::POSITION_TOP,
                'label' => __('At the top')
            ],
            [
                'value' => self::POSITION_BOTTOM,
                'label' => __('At the bottom')
            ],
            [
                'value' => self::POSITION_AFTER_CATEGORY,
                'label' => __('After the category filter')
            ]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $values = [];
        foreach ($this->toOptionArray() as $item) {
            $values[$item['value']] = $item['label'];
        }
        return $values;
    }
}

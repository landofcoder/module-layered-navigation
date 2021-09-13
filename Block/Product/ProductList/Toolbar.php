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

namespace Lof\LayeredNavigation\Block\Product\ProductList;

class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar{

    public function getTotalNum()
    {
        if( $this->checkParam() != null){
            return count($this->checkParam());
        }else{
            return $this->getCollection()->getSize();
        }
    }

    public function checkParam()
    {
        if( $this->getRequest()->getParam('in-stock') != null) {
            if (!isset($this->_current_stock_param)) {
                $filter = $this->getRequest()->getParam('in-stock');
                $this->_current_stock_param = $this->getStockCollection($filter);
            }
            return $this->_current_stock_param;
        }else{
            return null;
        }
    }

    public function getStockCollection($value)
    {
        $collection = $this->getCollection();
        $select = clone $collection->getSelect();
        if (strpos($value, ',') == false) {
            $select->where('stock_status_idx.stock_status = ?',$value);
        }
        $select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $select->reset(\Zend_Db_Select::LIMIT_COUNT);
        $select->reset(\Zend_Db_Select::LIMIT_OFFSET);

        $wherePart = $select->getPart(\Magento\Framework\DB\Select::WHERE);
        //remove e.entity_id part of where part
        foreach($wherePart as $id => $where) {
            if (strpos($where, 'e.entity_id IN') >= 0 || strpos($where, 'e.entity_id in') >= 0) {
                unset($wherePart[$id]);
            }
        }
        $select->setPart(\Magento\Framework\DB\Select::WHERE, $wherePart);
        
        $result = $collection->getConnection()->fetchAll($select);
        return $result;
    }

    public function getLastPageNum()
    {   
        if ($this->checkParam() != null) {
            if (count($this->checkParam()) > $this->getLimit()) {
                return ceil(count($this->checkParam())/$this->getLimit());
            } else {
                return 0;
            }
        } else {
            return parent::getLastPageNum();
        }
    }
}
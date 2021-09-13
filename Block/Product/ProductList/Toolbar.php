<?php

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
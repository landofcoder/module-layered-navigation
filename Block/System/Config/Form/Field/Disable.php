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
namespace Lof\LayeredNavigation\Block\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template;

class Disable extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $_remoteAddress;
    /**
     * @var \Lof\All\Helper\Data
     */
    protected $_helperAll;

    public function __construct(
        \Lof\All\Helper\Data $helperAll,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        Template\Context $context, array $data = [])
    {
        $this->_remoteAddress = $remoteAddress;
        $this->_helperAll = $helperAll;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        $license = $this->_helperAll->getLicense('Lof_LayeredNavigation');
        $ip = $this->_remoteAddress->getRemoteAddress();
        if ($license->getStatus() == 0) {
            if($ip != '127.0.0.1'){
                $element->setDisabled('disabled');
            }
        }
        return $element->getElementHtml();
    }
}
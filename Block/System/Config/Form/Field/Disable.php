<?php
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
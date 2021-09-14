<?php

namespace Lof\LayeredNavigation\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetup;

class UpgradeData implements UpgradeDataInterface
{
    /**
     *
     * @param Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetup $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Upgrades filter_stock attribute
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->eavSetupFactory->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, InstallData::ATTRIBUTE_CODE);
            $this->eavSetupFactory->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                InstallData::ATTRIBUTE_CODE,
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'In Stock',
                    'input' => 'select',
                    'class' => '',
                    'source' => \Lof\LayeredNavigation\Model\Config\Source\Options::class,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'default' => 1,
                    'searchable' => false,
                    'filterable' => true,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'is_filterable_in_grid'=> true,
                    'is_filterable_in_search' => 1,
                    'is_used_for_promo_rules' => 1,
                    'is_filterable' => 1,
                    'is_searchable' => 0,
                    'unique' => false,
                    'apply_to' => 'simple',
                    'is_user_defined' => true,
                    'position' => 100
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->updateApplyAttributes($setup);
        }
        $setup->endSetup();
    }

    public function updateApplyAttributes($setup)
    {
        $this->eavSetupFactory->updateAttribute('catalog_product', InstallData::ATTRIBUTE_CODE, 'apply_to', 'simple,configurable,bundle,grouped');
    }
}

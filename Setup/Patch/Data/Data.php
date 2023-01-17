<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ced\GoodMarket\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * Patch is mechanism, that allows to do atomic upgrade data changes
 */
class Data implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /** @var EavSetupFactory */
    private $eavSetupFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    private $eavAttribute;

    /**
     * Data Constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute
        // \Ced\GoodMarket\Controller\Adminhtml\Config\Save $save
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavAttribute = $eavAttribute;
//        $this->save=$save;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
//        $this->save->execute();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        /**
         * Add attributes to the eav/attribute
         */
        $groupName = 'GoodMarket';
        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);
        $eavSetup->addAttributeGroup($entityTypeId, $attributeSetId, $groupName, 1000);
        $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, $groupName);

        if (!$this->eavAttribute->getIdByCode('catalog_product', 'goodmarket_profile_id')) {
            $eavSetup->addAttribute(
                'catalog_product',
                'goodmarket_profile_id',
                [
                    'group' => 'GoodMarket',
                    'input' => 'text',
                    'type' => 'int',
                    'label' => 'GoodMarket Profile Id',
                    'backend' => '',
                    'visible' => true,
                    'required' => false,
                    'sort_order' => 10,
                    'user_defined' => true,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL
                ]
            );
        }
        if (!$this->eavAttribute->getIdByCode('catalog_product', 'goodmarket_product_id')) {
            $eavSetup->addAttribute(
                'catalog_product',
                'goodmarket_product_id',
                [
                    'group' => 'GoodMarket',
                    'input' => 'text',
                    'type' => 'text',
                    'label' => 'GoodMarket Product Id',
                    'backend' => '',
                    'visible' => true,
                    'required' => false,
                    'sort_order' => 10,
                    'user_defined' => true,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'note' => 'The ID associated with this product',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL
                ]
            );
        }
        if (!$this->eavAttribute->getIdByCode('catalog_product', 'goodmarket_product_status')) {
            $eavSetup->addAttribute(
                'catalog_product',
                'goodmarket_product_status',
                [
                    'group' => 'GoodMarket',
                    'input' => 'text',
                    'type' => 'text',
                    'label' => 'GoodMarket Product Status',
                    'backend' => '',
                    'visible' => true,
                    'required' => false,
                    'sort_order' => 10,
                    'user_defined' => true,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'note' => 'This Attribute will let you know the product status on GoodMarket.com',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL
                ]
            );
        }
        if (!$this->eavAttribute->getIdByCode('catalog_product', 'goodmarket_product_error')) {
            $eavSetup->addAttribute(
                'catalog_product',
                'goodmarket_product_error',
                [
                    'group' => 'GoodMarket',
                    'input' => 'text',
                    'type' => 'text',
                    'label' => 'GoodMarket Product Error',
                    'backend' => '',
                    'visible' => true,
                    'required' => false,
                    'sort_order' => 10,
                    'user_defined' => true,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'note' => 'Product Validation Report Error is being Shown',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL
                ]
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }
}

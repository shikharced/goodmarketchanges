<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_GoodMarket
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright © 2018 CedCommerce. All rights reserved.
 * @license     EULA http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class Attribute for form
 */
class Attribute extends AbstractSource
{
    /**
     * public function get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $magentoattributeCodeArray=[];
        $this->_objectManager=\Magento\Framework\App\ObjectManager::getInstance();
        $attributes = $this->_objectManager->create(\Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection::class)
            ->getItems();
        foreach ($attributes as $attribute) {
            $magentoattributeCodeArray[]=[
                'value'=> $attribute->getAttributecode(),
            'label'=>$attribute->getFrontendLabel()
            ];
        }
        return $magentoattributeCodeArray;
    }
}

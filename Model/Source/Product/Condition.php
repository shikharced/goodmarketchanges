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

namespace Ced\GoodMarket\Model\Source\Product;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class Condition for Form
 */
class Condition extends AbstractSource
{
    /**
     * Public function getAllOption
     *
     * @return array
     */
    public function getAllOptions()
    {
        return [
            [
                'value' => '',
                'label' => __('Please Select..')
            ],
            [
                'value' => 'NEW',
                'label' => __('NEW')
            ],
            [
                'value' => 'AS_NEW',
                'label' => __('AS_NEW')
            ],
            [
                'value' =>'GOOD',
                'label' => __('GOOD')
            ],
            [
                'value' => 'REASONABLE',
                'label' => __('REASONABLE')
            ],
            [
                'value' => 'MODERATE',
                'label' => __('MODERATE')
            ],
        ];
    }
}

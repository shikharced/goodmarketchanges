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
 * Class From
 * @package Ced\GoodMarket\Model\Source
 */
class CancellationCode extends AbstractSource
{
    /**
     * @return array
     */
    public function getAllOptions()
    {
        return [
            [
                'value' => 'OUT_OF_STOCK',
                'label' => __('OUT_OF_STOCK')
            ],
            [
                'value' => 'REQUESTED_BY_CUSTOMER',
                'label' => __('REQUESTED_BY_CUSTOMER')
            ],
            [
                'value' => 'BAD_CONDITION',
                'label' => __('BAD_CONDITION')
            ],
            [
                'value' => 'HIGHER_SHIPCOST',
                'label' => __('HIGHER_SHIPCOST')
            ],
            [
                'value' => 'INCORRECT_PRICE',
                'label' => __('INCORRECT_PRICE')
            ],
            [
                'value' => 'NOT_AVAIL_IN_TIME',
                'label' => __('NOT_AVAIL_IN_TIME')
            ],
            [
                'value' => 'NO_BOL_GUARANTEE',
                'label' => __('NO_BOL_GUARANTEE')
            ],
            [
                'value' => 'ORDERED_TWICE',
                'label' => __('ORDERED_TWICE')
            ],
            [
                'value' => 'RETAIN_ITEM',
                'label' => __('RETAIN_ITEM')
            ],
            [
                'value' => 'TECH_ISSUE',
                'label' => __('TECH_ISSUE')
            ],
            [
                'value' => 'UNFINDABLE_ITEM',
                'label' => __('UNFINDABLE_ITEM')
            ],
            [
                'value' => 'OTHER',
                'label' => __('OTHER')
            ],
        ];
    }
}

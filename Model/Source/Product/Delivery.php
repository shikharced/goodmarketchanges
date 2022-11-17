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
 * Class From
 * @package Ced\GoodMarket\Model\Source
 */
class Delivery extends AbstractSource
{
    /**
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
                'value' => '24uurs-23',
                'label' => __('24uurs-23')
            ],
            [
                'value' => '24uurs-22',
                'label' => __('24uurs-22')
            ],
            [
                'value' =>'24uurs-21',
                'label' => __('24uurs-21')
            ],
            [
                'value' => '24uurs-20',
                'label' => __('24uurs-20')
            ],
            [
                'value' => '24uurs-19',
                'label' => __('24uurs-19')
            ],
            [
                'value' => '24uurs-18',
                'label' => __('24uurs-18')
            ],
            [
                'value' => '24uurs-17',
                'label' => __('24uurs-17')
            ],
            [
                'value' =>'24uurs-16',
                'label' => __('24uurs-16')
            ],
            [
                'value' => '24uurs-15',
                'label' => __('24uurs-15')
            ],
            [
                'value' => '24uurs-14',
                'label' => __('24uurs-14')
            ],
            [
                'value' => '24uurs-13',
                'label' => __('24uurs-13')
            ],
            [
                'value' => '24uurs-12',
                'label' => __('24uurs-12')
            ],
            [
                'value' => '1-2d',
                'label' => __('1-2d')
            ],
            [
                'value' => '2-3d',
                'label' => __('2-3d')
            ],
            [
                'value' =>'3-5d',
                'label' => __('3-5d')
            ],
            [
                'value' => '4-8d',
                'label' => __('4-8d')
            ],
            [
                'value' => '1-8d',
                'label' => __('1-8d')
            ],
            [
                'value' => 'MijnLeverbelofte',
                'label' => __('MijnLeverbelofte')
            ],
            [
                'value' => 'VVB',
                'label' => __('VVB')
            ],
        ];
    }
}

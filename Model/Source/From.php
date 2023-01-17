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
 * Class From to getAllOptions
 */
class From extends AbstractSource
{
    /**
     * To getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        return [
            [
                'value' => null,
                'label' => __('Select')
            ],
            [
                'value' => '1',
                'label' => __('Last 1 day')
            ],
            [
                'value' => '2',
                'label' => __('Last 2 day')
            ],
            [
                'value' =>'3',
                'label' => __('Last 3 day')
            ],
            [
                'value' => '1_w',
                'label' => __('Last 1 week')
            ],
            [
                'value' => '2_w',
                'label' => __('Last 2 week')
            ],
            [
                'value' => '1_m',
                'label' => __('Last 1 month')
            ]
        ];
    }
}

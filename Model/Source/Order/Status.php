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

namespace Ced\GoodMarket\Model\Source\Order;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class Status to get API Status
 */
class Status extends AbstractSource
{

    // Api Status
    public const ALL = 'all';
    public const NEWORDER = 'new';
    public const PENDING = 'pending';
    public const HISTORIC = 'historic';
    public const CANCELLED='cancelled';
    public const FAILED='failed';

    // const STATUS = [
    //     self::ALL,
    //     self::CONFIRMED,
    //     self::PAYMENT_IN_PROGRESS,
    //     self::PARTIALLY_PAID,
    //     self::PAID,
    //     self::CANCELLED,
    //     self::INVALID,
    // ];

    /**
     * public function getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        return [
            [
                'value' => 'Success',
                'label' => __('Success')
            ],
            [
                'value' => 'Invoiced',
                'label' => __('Invoiced')
            ],
            [
                'value' => 'Shipped',
                'label' => __('Shipped')
            ],
            [
                'value' => 'failed',
                'label' => __('failed')
            ],
        ];
    }
}

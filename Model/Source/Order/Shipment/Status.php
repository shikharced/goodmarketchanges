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

namespace Ced\GoodMarket\Model\Source\Order\Shipment;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class Status of shimpments
 */
class Status extends AbstractSource
{
    public const ALL = 'all';
    public const TO_BE_AGREED = 'to_be_agreed';
    public const PENDING = 'pending';
    public const HANDLING = 'handling';
    public const READY_TO_SHIP = 'ready_to_ship';
    public const SHIPPED = 'shipped';
    public const DELIVERED = 'delivered';
    public const NOT_DELIVERED = 'not_delivered';
    public const NOT_VERIFIED = 'not_verified';
    public const CANCELLED = 'cancelled';
    public const CLOSED = 'closed';
    public const ACTIVE = 'active';

    public const STATUS = [
        self::ALL,
        self::TO_BE_AGREED,
        self::PENDING,
        self::HANDLING ,
        self::READY_TO_SHIP,
        self::SHIPPED,
        self::DELIVERED,
        self::NOT_DELIVERED,
        self::NOT_VERIFIED,
        self::CANCELLED,
        self::CLOSED,
        self::ACTIVE,
    ];

    /**
     * public function getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        return [
            [
                'value' => self::ALL,
                'label' => __('All')
            ],
            [
                'value' => self::TO_BE_AGREED,
                'label' => __('To Be Agreed')
            ],
            [
                'value' => self::PENDING,
                'label' => __('Pending')
            ],
            [
                'value' => self::HANDLING,
                'label' => __('Handling')
            ],
            [
                'value' => self::READY_TO_SHIP,
                'label' => __('Ready To Ship')
            ],
            [
                'value' => self::SHIPPED,
                'label' => __('Shipped')
            ],
            [
                'value' => self::DELIVERED,
                'label' => __('Delivered')
            ],
            [
                'value' => self::NOT_DELIVERED,
                'label' => __('Not Delivered')
            ],
            [
                'value' => self::NOT_VERIFIED,
                'label' => __('Not Verified')
            ],
            [
                'value' => self::CANCELLED,
                'label' => __('Cancelled')
            ],
            [
                'value' => self::CLOSED,
                'label' => __('Closed')
            ],
            [
                'value' => self::ACTIVE,
                'label' => __('Active')
            ]
        ];
    }
}

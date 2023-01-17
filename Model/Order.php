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

namespace Ced\GoodMarket\Model;

/**
 * Class Order to get ORders
 */
class Order extends \Magento\Framework\Model\AbstractModel
{
    public const NAME = "ced_goodmarket_order";

    public const COLUMN_ID = 'id';
    public const COLUMN_MAGENTO_ORDER_ID = 'magento_order_id';
    public const COLUMN_MAGENTO_INCREMENT_ID = 'magento_increment_id';
    public const COLUMN_MARKETPLACE_ORDER_ID = 'goodmarket_order_id';
    public const COLUMN_MARKETPLACE_SHIPMENT_ID = 'goodmarket_shipment_id';
    public const COLUMN_MARKETPLACE_DATE_CREATED = 'date_created';
    public const COLUMN_MARKETPLACE_SITE_ID = 'site_id';
    public const COLUMN_STATUS = 'status';
    public const COLUMN_FAILURE_REASON = 'reason';
    public const COLUMN_ORDER_DATA = 'order_data';
    public const COLUMN_SHIPMENT_DATA = 'shipment_data';
    public const COLUMN_CANCELLATION_DATA = 'cancellation_data';
    public const COLUMN_ORDER_ACCOUNT_CODE='account_code';
    /**
     * Function _construct
     *
     * @throws error
     */
    public function _construct()
    {
        $this->_init(\Ced\GoodMarket\Model\ResourceModel\Order::class);
    }

    /**
     * Function getByPurchaseOrderId
     *
     * @param string $poId
     * @return string
     */
    public function getByPurchaseOrderId($poId)
    {
        $order = $this->load($poId, self::COLUMN_MARKETPLACE_ORDER_ID);
        if ($order->getId() !== null) {
            return $order;
        }

        return null;
    }

    /**
     * Function loadByMagentoOrderId
     *
     * @param string $magentoOrderId
     * @return array
     */
    public function loadByMagentoOrderId($magentoOrderId)
    {
        $this->load($magentoOrderId, self::COLUMN_MAGENTO_ORDER_ID);
    }
}

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

class Order extends \Magento\Framework\Model\AbstractModel
{
    const NAME = "ced_goodmarket_order";

    const COLUMN_ID = 'id';
    const COLUMN_MAGENTO_ORDER_ID = 'magento_order_id';
    const COLUMN_MAGENTO_INCREMENT_ID = 'magento_increment_id';
    const COLUMN_MARKETPLACE_ORDER_ID = 'goodmarket_order_id';
    const COLUMN_MARKETPLACE_SHIPMENT_ID = 'goodmarket_shipment_id';
    const COLUMN_MARKETPLACE_DATE_CREATED = 'date_created';
    const COLUMN_MARKETPLACE_SITE_ID = 'site_id';
    const COLUMN_STATUS = 'status';
    const COLUMN_FAILURE_REASON = 'reason';
    const COLUMN_ORDER_DATA = 'order_data';
    const COLUMN_SHIPMENT_DATA = 'shipment_data';
    const COLUMN_CANCELLATION_DATA = 'cancellation_data';
    const COLUMN_ORDER_ACCOUNT_CODE='account_code';
    public function _construct()
    {
        $this->_init(\Ced\GoodMarket\Model\ResourceModel\Order::class);
    }

    public function getByPurchaseOrderId($poId)
    {
        $order = $this->load($poId, self::COLUMN_MARKETPLACE_ORDER_ID);
        if ($order->getId() !== null) {
            return $order;
        }

        return null;
    }

    public function loadByMagentoOrderId($magentoOrderId)
    {
        $this->load($magentoOrderId, self::COLUMN_MAGENTO_ORDER_ID);
    }
}

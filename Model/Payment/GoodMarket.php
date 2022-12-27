<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Range
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Model\Payment;

/**
 * class GoodMarket to get Payment source
 */
class GoodMarket extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * @var string
     */
    public const PAYMENT_METHOD_CODE = 'paybygoodmarket';
    
    /**
     * @var string
     */
    public $_code = self::PAYMENT_METHOD_CODE;

    /**
     * param $_canAuthorize
     *
     * @var bool
     */
    public $_canAuthorize = false;

    /**
     * param $_canCancelInvoice
     *
     * @var bool
     */
    public $_canCancelInvoice = false;

    /**
     * param $_canCapture
     *
     * @var bool
     */
    public $_canCapture = false;

    /**
     * param $_canCapturePartial
     *
     * @var bool
     */
    public $_canCapturePartial = false;

    /**
     * param $_canCreateBillingAgreement
     *
     * @var bool
     */
    public $_canCreateBillingAgreement = false;

    /**
     * param $_canFetchTransactionInfo
     *
     * @var bool
     */
    public $_canFetchTransactionInfo = false;

    /**
     * param $_canManageRecurringProfiles
     *
     * @var bool
     */
    public $_canManageRecurringProfiles = false;

    /**
     * @var bool
     */
    public $_canOrder = false;

    /**
     * param $_canRefund
     *
     * @var bool
     */
    public $_canRefund = false;

    /**
     * param $_canRefundInvoicePartial
     *
     * @var bool
     */
    public $_canRefundInvoicePartial = false;

    /**
     * param $_canReviewPayment
     *
     * @var bool
     */
    public $_canReviewPayment = false;

    /* START */
    /* Setting for disable from front-end. */
    /**
     * @var bool
     */
    public $_canUseCheckout = false;

    /**
     * param $_canUseForMultishipping
     *
     * @var bool
     */
    public $_canUseForMultishipping = false;

    /**
     * param _canUseInterna$l
     *
     * @var bool
     */
    public $_canUseInternal = false;

    /**
     * param $_canVoid
     *
     * @var bool
     */
    public $_canVoid = false;

    /**
     * param $_isGateway
     *
     * @var bool
     */
    public $_isGateway = false;

    /**
     * param $_isInitializeNeeded
     *
     * @var bool
     */
    public $_isInitializeNeeded = false;

    /* END */

    /**
     * Function isAvailable
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        return true;
    }

    /**
     * Function getCode
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }
}

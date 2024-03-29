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
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Model\ResourceModel;

/**
 * Class Profile to assign categories and products
 */
class Profile extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Profile construct.
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('goodmarket_profile', 'id');
    }
}

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
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Model\ResourceModel\Profile;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Public function condtructor
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _construct()
    {
        $this->_init(\Ced\GoodMarket\Model\Profile::class, \Ced\GoodMarket\Model\ResourceModel\Profile::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * Update status function
     *
     * @param string $dataToUpdate
     * @param string $condition
     */
    public function updateStatus($dataToUpdate, $condition)
    {
        $this->getConnection()->update(
            $this->getTable('goodmarket_profile'),
            $dataToUpdate,
            $condition
        );
    }
}

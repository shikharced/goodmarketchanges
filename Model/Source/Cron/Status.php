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
 * @category  Ced
 * @package   Ced_GoodMarket
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Model\Source\Cron;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class Status
 * @package Ced\GoodMarket\Model\Source\Cron\Status
 */
class Status extends AbstractSource
{
    const PENDING = 'pending';
    const FAILED = 'failed';
    const SUBMITTED = 'submitted';

    /**
     * @return array
     */
    public function getAllOptions()
    {
        return [
            [
                'value' => self::PENDING,
                'label' => __('Pending'),
            ],
            [
                'value' => self::FAILED,
                'label' => __('Failed'),
            ],
            [
                'value' => self::SUBMITTED,
                'label' => __('Submitted'),
            ]
        ];
    }
}

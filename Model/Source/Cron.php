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

namespace Ced\GoodMarket\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class ServiceUrl for url
 */
class Cron extends AbstractSource
{
    public const CRON_2MINUTES ='*/2 * * * *';
    public const CRON_15MINUTES = '*/15 * * * *';
    public const CRON_30MINUTES = '*/30 * * * *';
    public const CRON_HOURLY = '0 * * * *';
    public const CRON_2HOURLY = '0 */2 * * *';
    public const CRON_6HOURLY = '0 */2 * * *';
    public const CRON_12HOURLY = '0 0,12 * * *';
    public const CRON_DAILY = '0 0 * * *';

    /**
     * public function getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        $expressions = [
            [
                'label' => __('Every  2 Minutes'),
                'value' => self::CRON_2MINUTES
            ],
            [
                'label' => __('Every  15 Minutes'),
                'value' => self::CRON_15MINUTES
            ],
            [
                'label' => __('Every 30 Minutes'),
                'value' => self::CRON_30MINUTES
            ],
            [
                'label' => __('Every Hour'),
                'value' => self::CRON_HOURLY
            ],
            [
                'label' => __('Every 2 Hours'),
                'value' => self::CRON_2HOURLY
            ],
            [
                'label' => __('Every 6 Hours'),
                'value' => self::CRON_6HOURLY
            ],
            [
                'label' => __('Twice A Day'),
                'value' => self::CRON_12HOURLY
            ],
            [
                'label' => __('Once A Day'),
                'value' => self::CRON_DAILY
            ],
        ];

        return $expressions;
    }
}

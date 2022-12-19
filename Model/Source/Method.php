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
 * Class Method for form
 */
class Method extends AbstractSource
{
    /**
     * Public function getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        return [
            [
                'value' => 'FBR',
                'label' => __('FBR')
            ],
            [
                'value' => 'FBB',
                'label' => __('FBB')
            ],
        ];
    }
}

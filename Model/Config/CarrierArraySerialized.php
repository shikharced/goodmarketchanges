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
 * @package   Ced_EbayMultiAccount
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Model\Config;

/**
 * class Carrier Array Serialized 
 */
class CarrierArraySerialized extends \Magento\Config\Model\Config\Backend\Serialized
{

    /**
     * befpreSave func
     *
     * @return array
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        
        if (is_array($value)) {
            $value = $this->unique($value, 'magento_carrier');
        }
        $this->setValue($value);
        return parent::beforeSave();
    }

    /**
     * function unique
     *
     * @param $array
     * @param $key1
     * @return array
     */
    function unique($array, $key1)
    {
        $parsedArray = [];
        $i = 0;
        $keyArray = [];
        foreach ($array as $key => $val) {
            if (!isset($val[$key1])) {
                continue;
            }

            if (!in_array($val[$key1], $keyArray)) {
                $keyArray[$i] = $val[$key1];
                $parsedArray[$key] = $val;
            }
            $i++;
        }
        return $parsedArray;
    }
}

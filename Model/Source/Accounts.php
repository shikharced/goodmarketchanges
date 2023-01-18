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
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Model\Source;

/**
 * Class Profile form
 */
class Accounts extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Get GoodMarket product status labels array for option element
     *
     * @return array
     */
    public function getOptions()
    {
        $res = [];
        foreach ($this->getOptionArray() as $index => $value) {
            $res[] = ['value' => $index, 'label' => $value];
        }
        return $res;
    }

    /**
     * Public function getAlloptions
     *
     * @return array
     */
    public function getAllOptions()
    {

        $collection = \Magento\Framework\App\ObjectManager::getInstance()
        ->get(\Ced\GoodMarket\Model\Accounts::class)->getCollection();

        $data = [];
        foreach ($collection as $account) {
            $data[] = ['value' => $account->getId(), 'label' => $account->getAccountCode()];
        }
        return $data;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        $options = ['' => 'Please select the Account'];
        foreach ($this->getAllOptions() as $option) {
            $options[$option['value']] = (string)$option['label'];
        }
        return $options;
    }

    /**
     * Public function toOptionArray
     *
     * {@inheritdoc}
     */
    public function toOptionArray()
    {

        return $this->getOptions();
    }

    /**
     * Get GoodMarket product status labels array with empty value
     *
     * @return array
     */
    public function getAllOption()
    {
        $options = $this->getOptionArray();
        array_unshift($options, ['value' => '', 'label' => '']);
        return $options;
    }

    /**
     * Get GoodMarket product status
     *
     * @param string $optionId
     * @return null|string
     */
    public function getOptionText($optionId)
    {
        $options = $this->getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}

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
 * Class Profile source
 */
class Profile extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Get walmart product status labels array for option element
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
     * public function getAllOptions
     *
     * @return array
     */
    public function getAllOptions($accountId = 0)
    {
        $data = [];
        $collection = \Magento\Framework\App\ObjectManager::getInstance()
        ->get(\Ced\GoodMarket\Model\Profile::class)->getCollection();
        if ($accountId && $accountId > 0) {
            $collection->addFieldToFilter('account_id', $accountId);
            $data[] = ['value' => 0, 'label' => '--Not Assigned--'];
        }

        foreach ($collection as $profile) {
            $data[] = [
                'value' => $profile->getId(),
                'label' => $profile->getProfileName() . ' [' . $profile->getId() . ']'
            ];
        }
        return $data;
    }

    /**
     * Retrieve option array
     *
     * @param $accountId
     * @return array
     */
    public function getOptionArray($accountId = 0)
    {
        $options = [];
        foreach ($this->getAllOptions($accountId) as $option) {
            $options[$option['value']] = (string)$option['label'];
        }
        return $options;
    }

    /**
     * toOptionArray
     *
     * {@inheritdoc}
     */
    public function toOptionArray()
    {

        return $this->getOptions();
    }

    /**
     * Get walmart product status labels array with empty value
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
     * Get walmart product status
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

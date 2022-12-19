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

namespace Ced\GoodMarket\Model;

use \Magento\Framework\Model\AbstractModel;

/**
 * Class Profile for profiles
 */
class Profile extends AbstractModel
{

    public $productIds = [];

    /**
     * public function __construct
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
     * @param \Magento\Catalog\Model\Product\ActionFactory $productActionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Catalog\Model\Product\ActionFactory $productActionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->productCollection = $productCollection;
        $this->productActionFactory = $productActionFactory;
    }
    /**
     * public function _construct
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(\Ced\GoodMarket\Model\ResourceModel\Profile::class);
    }

    /**
     * public function loadByField
     *
     * @param $field
     * @param $value
     * @param string $additionalAttributes
     * @return $this
     */
    public function loadByField($field, $value, $additionalAttributes = '*')
    {
        $collection = $this->getResourceCollection()->addFieldToSelect($additionalAttributes);
        if (is_array($field) && is_array($value)) {
            foreach ($field as $key => $f) {
                if (isset($value[$key])) {
                    $collection->addFieldToFilter($f, $value[$key]);
                }
            }
        } else {
            $collection->addFieldToFilter($field, $value);
        }

        $collection->setCurPage(1)
            ->setPageSize(1);
        foreach ($collection as $object) {
            $this->load($object->getId());
            return $this;
        }
        return $this;
    }

    /**
     * public function getProductsPosition
     *
     * @return array
     */
    public function getProductsPosition()
    {
        if ($id = $this->getId()) {
            $ids = $this->productCollection->create()
                ->addAttributeToFilter('goodmarket_profile_id', ['eq' => $id])
                ->getAllIds();
            if (is_array($ids) && !empty($ids)) {
                $this->productIds = array_flip($ids);
            }
        } else {
            $this->productIds = [];
        }
        return $this->productIds;
    }

    /**
     * public function updateProducts
     *
     * @param $profileProducts
     */
    public function updateProducts($profileProducts)
    {
        if ($id = $this->getId()) {
            $oldIds = $this->productCollection->create()
                ->addAttributeToFilter('goodmarket_profile_id', ['eq' => $id])
                ->getAllIds();
            $productId = explode(',', $profileProducts);

            $newIds = array_diff($productId, $oldIds);
            $toBeRemoveIds = array_diff($oldIds, $productId);
            if (!empty($newIds)) {
                $this->productActionFactory->create()
                    ->updateAttributes($newIds, ['goodmarket_profile_id' => $id], 0);
            }

            if (!empty($toBeRemoveIds)) {
                $this->productActionFactory->create()
                    ->updateAttributes($toBeRemoveIds, ['goodmarket_profile_id' => ''], 0);
            }
        }
    }
}

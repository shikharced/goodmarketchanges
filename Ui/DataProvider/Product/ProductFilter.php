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
namespace Ced\GoodMarket\Ui\DataProvider\Product;
use Magento\Framework\Data\Collection;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;
class ProductFilter implements AddFilterToCollectionInterface
{
    public $request;
    public $profileProduct;
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Ced\GoodMarket\Helper\Config $config
    ) {
        $this->config=$config;
        $this->request = $request;
        $this->collection=$collectionFactory;
        $this->orderRepository = $orderRepository;
    }
    /**
     * {@inheritdoc}
     */
    public function addFilter(Collection $collection, $field, $condition = null)
    {
        try {
            $filters = $this->request->getParam('filters', []);
            if (isset($condition['eq']) && $condition['eq']) {
                if (isset($filters['product_offer_status'])) {
                    if ($filters['product_offer_status'] == 'Not-Uploaded') {
                        $collection->addAttributeToFilter('product_offer_id', ['null' => true]);
                    } else {
                        $collection->addAttributeToFilter('product_offer_id', ['neq' => '']);
                    }
                }
            }
        }catch(\Exception $e)
        {
          echo "<pre>";
          print_r($e->getMessage());
          die(__FILE__);
        }catch(\Error $e)
        {
            echo "<pre>";
            print_r($e->getMessage());
            die(__FILE__);
        }
    }
}
<?php
namespace Ced\GoodMarket\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\ProductCategoryList;
use Ced\GoodMarket\Model\ProfileFactory;
use Magento\Framework\ObjectManagerInterface;

/**
 * Observer class Product Save after
 */
class Productsaveafter implements ObserverInterface
{
    /**
     * Execute Function
     *
     * @param $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $_product = $observer->getProduct();  // you will get product object
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//        echo $product->getId();exit;
        if ($_product->getTypeId() == 'Simple') {
            $product = $objectManager->get(\Magento\Catalog\Model\Product::class)->load($_product->getId());
            $this->productCategory = $objectManager->get(\Magento\Catalog\Model\ProductCategoryList::class);
            $categoryIds = $this->productCategory->getCategoryIds($_product->getId());
//        echo '<pre>'; var_dump($product->getData('goodmarket_profile_id'));exit;
            foreach ($categoryIds as $magentoCat) {
                try {
                    $this->profileFactory = $objectManager->get(\Ced\GoodMarket\Model\ProfileFactory::class);
                    $profile = $this->profileFactory->create()->load($magentoCat, 'magento_category');
                    if ($profile->getId()) {
                        if ($product->getGoodmarketProfileId() == '') {
                            $product->setData('goodmarket_profile_id', $profile->getId());
                            $product->save();
                            break;
                        }
                    }
                } catch (\Exception $e) {
//                $this->logger->error($e->getMessage());
//                echo $e->getMessage(); exit;
                }
            }

        } else {
            $this->productCategory = $objectManager->get(\Magento\Catalog\Model\ProductCategoryList::class);
            $categoryIds = $this->productCategory->getCategoryIds($_product->getId());
//        echo '<pre>'; var_dump($product->getData('goodmarket_profile_id'));exit;
            foreach ($categoryIds as $magentoCat) {
                try {
                    $this->profileFactory = $objectManager->get(\Ced\GoodMarket\Model\ProfileFactory::class);
                    $profile = $this->profileFactory->create()->load($magentoCat, 'magento_category');
                    if ($profile->getId()) {
                        if ($_product->getGoodmarketProfileId() == '') {
                            $_product->setData('goodmarket_profile_id', $profile->getId());
                            $_product->save();
                            break;
                        }
                    }
                } catch (\Exception $e) {
//                $this->logger->error($e->getMessage());
//                echo $e->getMessage(); exit;
                }
            }
        }
        return $this;
    }
}

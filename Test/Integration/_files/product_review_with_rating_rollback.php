<?php

declare(strict_types=1);

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Framework\Registry $registry */
$registry = $objectManager->get(\Magento\Framework\Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);

/** @var \Magento\Review\Model\ResourceModel\Review $reviewResourceModel */
$reviewResourceModel = $objectManager->get(\Magento\Review\Model\ResourceModel\Review::class);

/** @var \Magento\Review\Model\ResourceModel\Rating $ratingResourceModel */
$ratingResourceModel = $objectManager->get(\Magento\Review\Model\ResourceModel\Rating::class);


$productSkuList = ['simple1', 'simple2', 'simple3'];
foreach ($productSkuList as $sku) {
    try {
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $product = $productRepository->get($sku, true);
        if ($product->getId()) {
            $ratingResourceModel->deleteAggregatedRatingsByProductId($product->getId());
            $reviewResourceModel->deleteReviewsByProductId($product->getId());
        }
    } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
        //Review or rating already removed
    }
}

$resolver = \Magento\TestFramework\Workaround\Override\Fixture\Resolver::getInstance();
$resolver->requireDataFixture('MageSuite_SortByRating::Test/Integration/_files/category_product_rollback.php');

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

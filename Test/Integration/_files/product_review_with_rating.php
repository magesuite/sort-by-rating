<?php

declare(strict_types=1);

$resolver = \Magento\TestFramework\Workaround\Override\Fixture\Resolver::getInstance();
$resolver->requireDataFixture('MageSuite_SortByRating::Test/Integration/_files/category_product.php');

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Framework\App\State $appState */
$appState = $objectManager->get(\Magento\Framework\App\State::class);
$appState->setAreaCode('frontend');

/** @var \Magento\Review\Model\ReviewFactory $reviewFactory */
$reviewFactory = $objectManager->get(\Magento\Review\Model\ReviewFactory::class);

/** @var \Magento\Review\Model\ResourceModel\Rating\CollectionFactory $ratingCollectionFactory */
$ratingCollectionFactory = $objectManager->get(\Magento\Review\Model\ResourceModel\Rating\CollectionFactory::class);

/** @var \Magento\Review\Model\ResourceModel\Rating\Option\CollectionFactory $optionCollectionFactory */
$optionCollectionFactory = $objectManager->get(\Magento\Review\Model\ResourceModel\Rating\Option\CollectionFactory::class);

/** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);

/** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder */
$searchCriteriaBuilder = $objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
$searchCriteriaBuilder->addFilter('sku', ['simple3', 'simple1', 'simple2'], 'in');

/** @var \Magento\Framework\Api\SearchCriteria $searchCriteria */
$searchCriteria = $searchCriteriaBuilder->create();
$productSearchResult = $productRepository->getList($searchCriteria);
$products = $productSearchResult->getItems();

/** @var \Magento\Store\Model\StoreManagerInterface $storeId */
$storeId = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class)
    ->getStore()
    ->getId();

/** @var \Magento\Review\Model\ResourceModel\Rating\Collection $ratingCollection */
$ratingCollection = $ratingCollectionFactory->create()
    ->addFieldToFilter('rating_code', 'Quality');

$qualityRating = $ratingCollection->getFirstItem();
$qualityRating->setStores([$storeId])
    ->setIsActive(1)
    ->save();

$ratingConfigForProducts = [
    [
        'sku' => 'simple3',
        'reviews_count' => 5,
        'each_review_quality_rating' => 4
    ],
    [
        'sku' => 'simple1',
        'reviews_count' => 5,
        'each_review_quality_rating' => 2,
    ],
    [
        'sku' => 'simple2',
        'reviews_count' => 4,
        'each_review_quality_rating' => 5,
    ],
];

/* @var \Magento\Catalog\Api\Data\ProductInterface $product */
foreach ($products as $product) {
    $sku = $product->getSku();
    $foundKey = array_search($sku, array_column($ratingConfigForProducts, 'sku'));
    $ratingConfigForProduct = $ratingConfigForProducts[$foundKey];
    for ($appliedReviews = 0; $appliedReviews < $ratingConfigForProduct['reviews_count']; $appliedReviews++) {

        /** @var \Magento\Review\Model\Review $review */
        $review = $reviewFactory->create();

        $review->setEntityId($review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE))
            ->setEntityPkValue($product->getId())
            ->setTitle('Review Summary')
            ->setDetail('Review text')
            ->setNickname('Nickname')
            ->setStatusId(\Magento\Review\Model\Review::STATUS_APPROVED)
            ->setStoreId($storeId)
            ->setStores([$storeId])
            ->save();

        /** @var \Magento\Review\Model\ResourceModel\Rating\Option\Collection $ratingOption */
        $ratingOption = $optionCollectionFactory->create()
            ->setPageSize(1)
            ->setCurPage($ratingConfigForProduct['each_review_quality_rating'])
            ->addRatingFilter($qualityRating->getId())
            ->getFirstItem();

        $qualityRating->setReviewId($review->getId())
            ->addOptionVote($ratingOption->getId(), $product->getId());

        $review->aggregate();
        $review->save();
    }
}

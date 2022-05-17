<?php

namespace MageSuite\SortByRating\Test\Integration\Controller\Category;

class SortingTest extends \Magento\TestFramework\TestCase\AbstractController
{
    public function setUp(): void
    {
        parent::setUp();
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * @magentoDataFixture MageSuite_SortByRating::Test/Integration/_files/product_review_with_rating.php
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     * @magentoConfigFixture default_store catalog/review/active 0
     * @magentoConfigFixture smile_elasticsuite_sorting_settings/general/min_count 4
     * @dataProvider productListSortOrderDataProvider
     * @param int $categoryId
     * @param string $sortBy
     * @param string $direction
     * @param array $expectation
     * @return void
     */
    public function testCategoryViewSortByCountAndRatingSummary(
        int $categoryId,
        string $sortBy,
        string $direction,
        array $expectation
    ): void {
        $this->objectManager->create(\Magento\CatalogSearch\Model\Indexer\Fulltext\Processor::class)
            ->reindexAll();

        $this->getRequest()
            ->setMethod(\Magento\Framework\App\Request\Http::METHOD_GET)
            ->setParams([
                'product_list_order' => $sortBy,
                'product_list_dir' => $direction
            ]);

        $this->dispatch("catalog/category/view/id/{$categoryId}");

        $items = $this->objectManager->get(\Magento\Catalog\Block\Product\ListProduct::class)
                ->getLoadedProductCollection()
                ->getItems();

        $sortedListSKU = array_map(function ($item){
            return $item->getSku();
        }, $items);

        $sortedListSKU = array_values($sortedListSKU);

        $this->assertEquals($expectation, $sortedListSKU);
    }

    /**
     * @return array
     */
    public function productListSortOrderDataProvider(): array
    {
        return [
            'default_order_reviews_count_asc' => [
                'categoryId' => 333,
                'sort' => 'reviews_count',
                'direction' => 'asc',
                'expectation' => ['simple2', 'simple3', 'simple1']
            ],
            'default_order_reviews_count_desc' => [
                'categoryId' => 333,
                'sort' => 'reviews_count',
                'direction' => 'desc',
                'expectation' => ['simple1', 'simple3', 'simple2']
            ],
            'default_order_ratings_summary_asc' => [
                'categoryId' => 333,
                'sort' => 'ratings_summary',
                'direction' => 'asc',
                'expectation' => ['simple2', 'simple1', 'simple3']
            ],
            'default_order_ratings_summary_desc' => [
                'categoryId' => 333,
                'sort' => 'ratings_summary',
                'direction' => 'desc',
                'expectation' => ['simple3', 'simple1', 'simple2']
            ],
        ];
    }
}

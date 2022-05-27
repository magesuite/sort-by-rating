<?php
declare(strict_types=1);

namespace MageSuite\SortByRating\Plugin\Smile\ElasticsuiteRating\Model\ResourceModel\Product\Indexer\Fulltext\Datasource\RatingData;

class AddFilterByMinReviewsCount
{
    protected \Magento\Framework\App\ResourceConnection $resourceConnection;

    protected \MageSuite\SortByRating\Helper\MinReviewsCountConfiguration $minReviewsCountConfiguration;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \MageSuite\SortByRating\Helper\MinReviewsCountConfiguration $minReviewsCountConfiguration
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->minReviewsCountConfiguration = $minReviewsCountConfiguration;
    }

    public function afterLoadRatingData(
        \Smile\ElasticsuiteRating\Model\ResourceModel\Product\Indexer\Fulltext\Datasource\RatingData $subject,
        $result,
        $storeId,
        $productIds
    ): array {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                ['res' => $connection->getTableName('review_entity_summary')],
                [
                    'entity_pk_value as product_id',
                    'avg(rating_summary) as ratings_summary',
                ]
            )
            ->where('res.store_id = ?', $storeId)
            ->where('res.entity_pk_value IN(?)', $productIds)
            ->where('res.reviews_count > ?', $this->minReviewsCountConfiguration->getMinReviewsCount())
            ->group('entity_pk_value');

        return $connection->fetchAll($select);
    }
}

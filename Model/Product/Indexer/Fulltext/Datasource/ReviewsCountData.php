<?php
declare(strict_types=1);

namespace MageSuite\SortByRating\Model\Product\Indexer\Fulltext\Datasource;

class ReviewsCountData implements \Smile\ElasticsuiteCore\Api\Index\DatasourceInterface
{
    public const REVIEWS_COUNT_PROPERTY_NAME = 'reviews_count';

    protected \MageSuite\SortByRating\Model\ResourceModel\Product\Indexer\Fulltext\Datasource\ReviewsCountData $reviewsCountData;

    public function __construct(
        \MageSuite\SortByRating\Model\ResourceModel\Product\Indexer\Fulltext\Datasource\ReviewsCountData $reviewsCountData
    ) {
        $this->reviewsCountData = $reviewsCountData;
    }

    public function addData($storeId, array $indexData)
    {
        $reviewsCountData = $this->reviewsCountData->loadData($storeId, array_keys($indexData));

        foreach ($indexData as &$indexDataRow) {

            if (array_key_exists($indexDataRow['entity_id'], $reviewsCountData)) {
                $indexDataRow[self::REVIEWS_COUNT_PROPERTY_NAME] = $reviewsCountData[$indexDataRow['entity_id']];
            } else {
                $indexDataRow[self::REVIEWS_COUNT_PROPERTY_NAME] = 0;
            }

            if (!isset($indexData[$indexDataRow['entity_id']]['indexed_attributes'])) {
                $indexData[$indexDataRow['entity_id']]['indexed_attributes'] = [self::REVIEWS_COUNT_PROPERTY_NAME];
            } elseif (!in_array(self::REVIEWS_COUNT_PROPERTY_NAME, $indexData[$indexDataRow['entity_id']]['indexed_attributes'])) {
                $indexData[$indexDataRow['entity_id']]['indexed_attributes'][] = self::REVIEWS_COUNT_PROPERTY_NAME;
            }

        }

        return $indexData;
    }
}

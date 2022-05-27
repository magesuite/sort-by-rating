<?php
declare(strict_types=1);

namespace MageSuite\SortByRating\Model\ResourceModel\Product\Indexer\Fulltext\Datasource;

class ReviewsCountData extends \Smile\ElasticsuiteCatalog\Model\ResourceModel\Eav\Indexer\Indexer
{
    public function loadData($storeId, $productIds): array
    {
        $select = $this->getConnection()->select()
            ->from(
                ['res' => $this->getTable('review_entity_summary')],
                [
                    'entity_pk_value as product_id',
                    'reviews_count',
                ]
            )
            ->where('res.store_id = ?', $storeId)
            ->where('res.entity_pk_value IN(?)', $productIds);

        return $this->getConnection()->fetchPairs($select);
    }
}

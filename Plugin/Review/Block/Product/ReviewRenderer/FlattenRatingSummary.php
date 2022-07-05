<?php

namespace MageSuite\SortByRating\Plugin\Review\Block\Product\ReviewRenderer;

class FlattenRatingSummary
{
    public function afterGetRatingSummary(
        \Magento\Review\Block\Product\ReviewRenderer $subject,
        $result
    ) {
        return $result instanceof \Magento\Framework\DataObject ? $result->getRatingSummary() : $result;
    }
}

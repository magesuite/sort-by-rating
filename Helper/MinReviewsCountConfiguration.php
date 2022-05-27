<?php
declare(strict_types=1);

namespace MageSuite\SortByRating\Helper;

class MinReviewsCountConfiguration
{
    public const XML_CONFIG_PATH = 'smile_elasticsuite_sorting_settings/general/min_count';

    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getMinReviewsCount(): int
    {
        return (int) $this->scopeConfig->getValue(self::XML_CONFIG_PATH);
    }
}

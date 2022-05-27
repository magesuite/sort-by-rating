<?php
namespace MageSuite\SortByRating\Setup\Patch\Data;

class AddReviewsCountAttribute implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    public const ATTRIBUTE_CODE = 'reviews_count';

    protected \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup;

    protected \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory;

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,
            self::ATTRIBUTE_CODE,
            [
                'type'                       => 'decimal',
                'label'                      => 'Product Reviews Count',
                'input'                      => 'hidden',
                'global'                     => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'required'                   => false,
                'default'                    => 0,
                'visible'                    => true,
                'sort_order'                 => 300,
                'visible_on_front'           => 0,
                'searchable'                 => 1,
                'visible_in_advanced_search' => 0,
                'filterable'                 => 0,
                'filterable_in_search'       => 0,
                'is_used_in_grid'            => 0,
                'is_visible_in_grid'         => 0,
                'is_filterable_in_grid'      => 0,
                'used_for_sort_by'           => 1,
            ]
        );

        $eavSetup->updateAttribute(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE, 'ratings_summary', 'is_filterable', 0);
        $eavSetup->updateAttribute(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE, 'ratings_summary', 'is_filterable_in_search', 0);
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}

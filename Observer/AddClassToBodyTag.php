<?php
namespace MageSuite\SortByRating\Observer;

class AddClassToBodyTag implements \Magento\Framework\Event\ObserverInterface
{
    const RATINGS_SUMMARY = 'ratings_summary';
    const REVIEWS_COUNT = 'reviews_count';
    const CSS_CLASS_BODY = 'rating-sorting-enabled';

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\View\Page\Config $pageConfig
    ) {
        $this->request = $request;
        $this->pageConfig = $pageConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        $action = $event->getData("full_action_name");

        if ($action !== 'catalog_category_view') {
            return false;
        }

        $sortAttribute = $this->request->getParam('product_list_order');

        if ($sortAttribute !== self::RATINGS_SUMMARY && $sortAttribute !== self::REVIEWS_COUNT) {
            return false;
        }

        $this->pageConfig->addBodyClass(self::CSS_CLASS_BODY);
    }
}

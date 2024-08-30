<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlogCategory;

use Shopware\Core\Content\Seo\SeoUrlUpdater;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EEComBlogCategorySeoUrlListener implements EventSubscriberInterface
{
    /**
     * @var SeoUrlUpdater
     */
    private $seoUrlUpdater;

    public function __construct(SeoUrlUpdater $seoUrlUpdater)
    {
        $this->seoUrlUpdater = $seoUrlUpdater;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'eecom_blog_category.written' => 'onBlogCategoryUpdated',
        ];
    }

    public function onBlogCategoryUpdated(EntityWrittenEvent $event): void
    {
        $this->seoUrlUpdater->update(EEComBlogCategorySeoUrlRoute::ROUTE_NAME, $event->getIds());
    }
}

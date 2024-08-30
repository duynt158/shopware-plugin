<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog;


use Shopware\Core\Content\Seo\SeoUrlUpdater;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EEComBlogSeoUrlListener implements EventSubscriberInterface
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
            'eecom_blog.written' => 'onBlogUpdated',
        ];
    }

    public function onBlogUpdated(EntityWrittenEvent $event): void
    {

        $this->seoUrlUpdater->update(EEComBlogSeoUrlRoute::ROUTE_NAME, $event->getIds());
    }
}

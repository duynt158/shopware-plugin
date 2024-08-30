<?php declare(strict_types=1);

namespace EECom\EEComBlog\Elasticsearch\EEComBlog;


use EECom\EEComBlog\Core\Content\EEComBlog\Events\EEComBlogIndexerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Elasticsearch\Framework\Indexing\ElasticsearchIndexer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EEComBlogUpdater implements EventSubscriberInterface
{
    private ElasticsearchIndexer $indexer;

    private EntityDefinition $definition;

    public function __construct(ElasticsearchIndexer $indexer, EntityDefinition $definition)
    {
        $this->indexer = $indexer;
        $this->definition = $definition;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EEComBlogIndexerEvent::class => 'update',
        ];
    }

    public function update(EEComBlogIndexerEvent $event): void
    {

        $this->indexer->updateIds(
            $this->definition,
            array_unique(array_merge($event->getIds()))
        );
    }
}

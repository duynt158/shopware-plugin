<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\DataAbstractionLayer;

use Doctrine\DBAL\Connection;
use EECom\EEComBlog\Core\Content\EEComBlog\Events\EEComBlogIndexerEvent;
use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use Shopware\Core\Content\Product\DataAbstractionLayer\SearchKeywordUpdater;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IterableQuery;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IteratorFactory;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexer;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexingMessage;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\InheritanceUpdater;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\ManyToManyIdFieldUpdater;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EEComBlogIndexer extends EntityIndexer
{
    public const INHERITANCE_UPDATER = 'eecom_blog.inheritance';
    public const MANY_TO_MANY_ID_FIELD_UPDATER = 'eecom_blog.many-to-many-id-field';
    public const SEARCH_KEYWORD_UPDATER = 'eecom_blog.search-keyword';

    private IteratorFactory $iteratorFactory;

    private EntityRepositoryInterface $repository;

    private Connection $connection;

    private InheritanceUpdater $inheritanceUpdater;

    private SearchKeywordUpdater $searchKeywordUpdater;

    private ManyToManyIdFieldUpdater $manyToManyIdFieldUpdater;


    private EventDispatcherInterface $eventDispatcher;


    public function __construct(
        IteratorFactory           $iteratorFactory,
        EntityRepositoryInterface $repository,
        Connection                $connection,
        InheritanceUpdater        $inheritanceUpdater,
        SearchKeywordUpdater      $searchKeywordUpdater,
        ManyToManyIdFieldUpdater  $manyToManyIdFieldUpdater,
        EventDispatcherInterface  $eventDispatcher
    )
    {
        $this->iteratorFactory = $iteratorFactory;
        $this->repository = $repository;
        $this->connection = $connection;
        $this->searchKeywordUpdater = $searchKeywordUpdater;
        $this->inheritanceUpdater = $inheritanceUpdater;
        $this->manyToManyIdFieldUpdater = $manyToManyIdFieldUpdater;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getName(): string
    {
        return 'eecom_blog.indexer';
    }

    /**
     * @param array|null $offset
     *
     * @deprecated tag:v6.5.0 The parameter $offset will be native typed
     */
    public function iterate(/*?array */ $offset): ?EntityIndexingMessage
    {
        $iterator = $this->getIterator($offset);

        $ids = $iterator->fetch();

        if (empty($ids)) {
            return null;
        }

        return new EEComBlogIndexingMessage(array_values($ids), $iterator->getOffset());
    }

    public function update(EntityWrittenContainerEvent $event): ?EntityIndexingMessage
    {

        $updates = $event->getPrimaryKeys(EEComBlogDefinition::ENTITY_NAME);

        if (empty($updates)) {
            return null;
        }


        return new EEComBlogIndexingMessage(array_values($updates), null, $event->getContext());
    }

    public function getDecorated(): EntityIndexer
    {
        throw new DecorationPatternException(self::class);
    }

    public function handle(EntityIndexingMessage $message): void
    {

        $ids = $message->getData();
        $ids = array_unique(array_filter($ids));

        if (empty($ids)) {
            return;
        }

        $context = $message->getContext();

        $this->connection->beginTransaction();

        $all = array_filter(array_unique(array_merge($ids)));

        $this->manyToManyIdFieldUpdater->update(EEComBlogDefinition::ENTITY_NAME, $ids, $context);
        $this->searchKeywordUpdater->update(array_merge($ids), $context);

        $this->connection->executeStatement(
            'UPDATE eecom_blog SET updated_at = :now WHERE id IN (:ids)',
            ['ids' => Uuid::fromHexToBytesList($all), 'now' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)],
            ['ids' => Connection::PARAM_STR_ARRAY]
        );

        $this->connection->commit();

        $this->eventDispatcher->dispatch(new EEComBlogIndexerEvent($ids, $context));


    }


    private function getIterator(?array $offset): IterableQuery
    {
        return $this->iteratorFactory->createIterator($this->repository->getDefinition(), $offset);
    }
}

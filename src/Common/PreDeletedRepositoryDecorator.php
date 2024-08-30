<?php declare(strict_types=1);

namespace EECom\EEComBlog\Common;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResultCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Write\CloneBehavior;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PreDeletedRepositoryDecorator implements EntityRepositoryInterface
{
    private EntityRepositoryInterface $decorated;

    private EventDispatcherInterface $dispatcher;

    public function __construct(
        EntityRepositoryInterface $decorated,
        EventDispatcherInterface  $dispatcher
    )
    {
        $this->decorated = $decorated;
        $this->dispatcher = $dispatcher;
    }

    public function getDefinition(): EntityDefinition
    {
        return $this->decorated->getDefinition();
    }

    public function aggregate(Criteria $criteria, Context $context): AggregationResultCollection
    {
        return $this->decorated->aggregate($criteria, $context);
    }

    public function searchIds(Criteria $criteria, Context $context): IdSearchResult
    {
        return $this->decorated->searchIds($criteria, $context);
    }

    public function clone(string $id, Context $context, ?string $newId = null, ?CloneBehavior $behavior = null): EntityWrittenContainerEvent
    {
        return $this->decorated->clone($id, $context, $newId, $behavior);
    }

    public function search(Criteria $criteria, Context $context): EntitySearchResult
    {
        return $this->decorated->search($criteria, $context);
    }

    public function update(array $data, Context $context): EntityWrittenContainerEvent
    {
        return $this->decorated->update($data, $context);
    }

    public function upsert(array $data, Context $context): EntityWrittenContainerEvent
    {
        return $this->decorated->upsert($data, $context);
    }

    public function create(array $data, Context $context): EntityWrittenContainerEvent
    {
        return $this->decorated->create($data, $context);
    }

    public function delete(array $data, Context $context): EntityWrittenContainerEvent
    {
        $writeResults = [];
        foreach ($data as $key => $entry) {
            $writeResults[] = new EntityWriteResult(
                $entry['id'],
                $entry,
                $this->getDefinition()->getEntityName(),
                EntityWriteResult::OPERATION_DELETE
            );
        }

        $event = new PreDeletedEvent(
            $this->getDefinition()->getEntityName(),
            $writeResults,
            $context,
            []
        );

        $this->dispatcher->dispatch(
            $event,
            $this->getDefinition()->getEntityName() . '.pre-delete'
        );

        return $this->decorated->delete($data, $context);
    }

    public function createVersion(string $id, Context $context, ?string $name = null, ?string $versionId = null): string
    {
        return $this->decorated->createVersion($id, $context, $name, $versionId);
    }

    public function merge(string $versionId, Context $context): void
    {
        $this->decorated->merge($versionId, $context);
    }
}

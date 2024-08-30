<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\SalesChannel\Listing;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogCollection;
use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class EEComBlogListingLoader
{
    /**
     * @var SalesChannelRepositoryInterface
     */
    private $repository;


    public function __construct(
        SalesChannelRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    public function load(Criteria $origin, SalesChannelContext $context): EntitySearchResult
    {
        $criteria = clone $origin;


        $context->getContext()->addState(Context::STATE_ELASTICSEARCH_AWARE);
        $ids = $this->repository->searchIds($criteria, $context);

        $aggregations = $this->repository->aggregate($criteria, $context);

        // no products found, no need to continue
        if (empty($ids->getIds())) {
            return new EntitySearchResult(
                EEComBlogDefinition::ENTITY_NAME,
                0,
                new EEComBlogCollection(),
                $aggregations,
                $origin,
                $context->getContext()
            );
        }

        $variantIds = $ids->getIds();

        $mapping = array_combine($ids->getIds(), $ids->getIds());


        $read = $criteria->cloneForRead($variantIds);

        $entities = $this->repository->search($read, $context);

        $this->addExtensions($ids, $entities, $mapping);

        return new EntitySearchResult(
            EEComBlogDefinition::ENTITY_NAME,
            $ids->getTotal(),
            $entities->getEntities(),
            $aggregations,
            $origin,
            $context->getContext()
        );
    }


    private function addExtensions(IdSearchResult $ids, EntitySearchResult $entities, array $mapping): void
    {
        foreach ($ids->getExtensions() as $name => $extension) {
            $entities->addExtension($name, $extension);
        }

        foreach ($ids->getIds() as $id) {
            if (!isset($mapping[$id])) {
                continue;
            }

            // current id was mapped to another variant
            if (!$entities->has($mapping[$id])) {
                continue;
            }

            /** @var Entity $entity */
            $entity = $entities->get($mapping[$id]);

            // get access to the data of the search result
            $entity->addExtension('search', new ArrayEntity($ids->getDataOfId($id)));
        }
    }
}

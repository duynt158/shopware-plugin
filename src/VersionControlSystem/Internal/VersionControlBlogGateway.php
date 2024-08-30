<?php declare(strict_types=1);

namespace EECom\EEComBlog\VersionControlSystem\Internal;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use EECom\EEComBlog\VersionControlSystem\Data\EEComBlogActivityCollection;
use EECom\EEComBlog\VersionControlSystem\Data\EEComBlogDraftCollection;
use TypeError;

class VersionControlBlogGateway
{
    private const FALLBACK_DRAFT_NAME = '-';

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $draftRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $activityRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogRepository;

    public function __construct(
        EntityRepositoryInterface $draftRepository,
        EntityRepositoryInterface $activityRepository,
        EntityRepositoryInterface $blogRepository
    )
    {
        $this->draftRepository = $draftRepository;
        $this->activityRepository = $activityRepository;
        $this->blogRepository = $blogRepository;
    }

    public function searchActivities(Criteria $criteria, Context $context): EEComBlogActivityCollection
    {
        return $this->activityRepository
            ->search($criteria, $context)
            ->getEntities();
    }

    public function updateActivities(array $data, Context $context): EntityWrittenContainerEvent
    {

        return $this->activityRepository
            ->update($data, $context);
    }

    public function createActivities(array $data, Context $context): EntityWrittenContainerEvent
    {
        return $this->activityRepository
            ->create($data, $context);
    }

    public function searchDrafts(Criteria $criteria, Context $context): EEComBlogDraftCollection
    {
        return $this->draftRepository
            ->search($criteria, $context)
            ->getEntities();
    }

    public function updateDrafts(array $data, Context $context): EntityWrittenContainerEvent
    {
        return $this->draftRepository
            ->update($data, $context);
    }

    public function createDrafts(array $data, Context $context): EntityWrittenContainerEvent
    {
        return $this->draftRepository
            ->create($data, $context);
    }

    public function deleteDrafts(array $data, Context $context): EntityWrittenContainerEvent
    {
        return $this->draftRepository
            ->delete($data, $context);
    }

    public function fetchInheritedDraftData(string $id, Context $context): array
    {

        /** @var EEComBlogEntity $blog */
        $blog = $this->blogRepository
            ->search(CriteriaFactory::withIds($id), $context)
            ->first();


        return [
            'name' => $this->getBlogName($blog),
            'eecomBlogId' => $blog->getId()
        ];
    }

    private function getBlogName(EEComBlogEntity $blog): string
    {
        try {
            return $blog->getName();
        } catch (TypeError $e) {
            return self::FALLBACK_DRAFT_NAME;
        }
    }
}

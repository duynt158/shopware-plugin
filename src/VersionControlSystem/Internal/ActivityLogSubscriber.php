<?php declare(strict_types=1);

namespace EECom\EEComBlog\VersionControlSystem\Internal;

use DateTime;
use EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogTranslation\EEComBlogTranslationDefinition;
use EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogVisibility\EEComBlogVisibilityDefinition;
use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use EECom\EEComBlog\Core\Content\EEComBlogCategory\EEComBlogCategoryDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsPageTranslation\CmsPageTranslationDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSlotTranslation\CmsSlotTranslationDefinition;
use Shopware\Core\Content\Cms\CmsPageDefinition;
use Shopware\Core\Content\Cms\CmsPageEvents;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Api\Context\ContextSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Struct\ArrayEntity;
use EECom\EEComBlog\Common\PreDeletedEvent;
use EECom\EEComBlog\Common\UpdateChangeContextExtension;
use EECom\EEComBlog\VersionControlSystem\Exception\NoDraftFound;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function array_merge;
use function array_reverse;

class ActivityLogSubscriber implements EventSubscriberInterface
{
    private EntityRepositoryInterface $pageRepository;

    private DefinitionInstanceRegistry $definitionRegistry;

    private VersionControlBlogGateway $versionControlBlogGateway;

    /**
     * @var string[]
     */
    private array $pageIdMap = [];

    public function __construct(
        DefinitionInstanceRegistry $definitionRegistry,
        VersionControlBlogGateway  $versionControlBlogGateway,
        EntityRepositoryInterface  $pageRepository
    )
    {
        $this->definitionRegistry = $definitionRegistry;
        $this->versionControlBlogGateway = $versionControlBlogGateway;
        $this->pageRepository = $pageRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // update & insert
            EEComBlogDefinition::ENTITY_NAME . '.written' => 'logActivityOnBlogWriteEvent',
            EEComBlogTranslationDefinition::ENTITY_NAME . '.written' => 'logActivityOnBlogWriteEvent'
        ];
    }

    public function rememberActivityOnBlogDeleteEvent(PreDeletedEvent $event): void
    {
        $context = $event->getContext();
        $writeResults = $event->getWriteResults();

        $this->storeBlogIds($writeResults, $context);
    }

    public function logActivityOnBlogDeleteEvent(EntityDeletedEvent $event): void
    {
        $context = $event->getContext();
        $writeResults = $event->getWriteResults();
        $filteredWriteResults = $this->filterOnlyRememberedWriteResults($writeResults);

        $this->writeLogActivity($filteredWriteResults, $context);
    }

    public function logActivityOnBlogWriteEvent(EntityWrittenEvent $event): void
    {
        $context = $event->getContext();
        $writeResults = $event->getWriteResults();

        $this->storeBlogIds($writeResults, $context);
        $this->writeLogActivity($writeResults, $context);
    }

    /**
     * @param EntityWriteResult[] $writeResults
     */
    private function storeBlogIds(array $writeResults, Context $context): void
    {
        foreach ($writeResults as $writeResult) {
            $affectedEntity = WriteResultExtractor::extractAffectedEntity($writeResult);

            $key = $this->getIdMapKey($affectedEntity);
            $blogId = $this->fetchBlogIdByAffectedEntity($affectedEntity, $context);
            $this->pageIdMap[$key] = $blogId;
        }
    }

    /**
     * @param EntityWriteResult[] $writeResults
     */
    private function writeLogActivity(array $writeResults, Context $context): void
    {
        $source = $context->getSource();

        if (!$source instanceof AdminApiSource) {
            return;
        }

        try {
            $draftVersion = $this->determineDraftVersion($context);
        } catch (NoDraftFound $exception) {
            return;
        }

        $pageIdToDetailMap = $this->extractDetails($writeResults, $context);
        $pageIdToActivityMap = $this->loadActivities($pageIdToDetailMap, $draftVersion, $context);

        $this->writeActivityDetails($pageIdToDetailMap, $pageIdToActivityMap, $source, $draftVersion, $context);

        $this->pageIdMap = [];
    }

    private function createNewActivity(string $blogId, ?string $draftVersion, array $details, Context $context): void
    {
        $context->scope(Context::SYSTEM_SCOPE, function (Context $systemContext) use ($details, $blogId, $draftVersion): void {
            $this->versionControlBlogGateway->createActivities([[
                'draftVersion' => $draftVersion,
                'details' => $details,
                'eecomBlogId' => $blogId,
                'userId' => $systemContext->getSource()->getUserId(),
                'name' => $this->fetchDraftName($blogId, $draftVersion, $systemContext),
            ]], $systemContext->createWithVersionId(Defaults::LIVE_VERSION));
        });
    }

    private function updateExistingActivity(ArrayEntity $activity, array $details, Context $context): void
    {
        $context->scope(Context::SYSTEM_SCOPE, function (Context $systemContext) use ($activity, $details): void {
            $this->versionControlBlogGateway->updateActivities([[
                'id' => $activity->getId(),
                'details' => $this->mergeActivityDetails($details, $activity['details']),
            ]], $systemContext->createWithVersionId(Defaults::LIVE_VERSION));
        });
    }

    private function extractDetailsFromWriteResults(EntityWriteResult $writeResult, Context $context): ?array
    {
        $payload = $writeResult->getPayload();

        if ($this->isAllowedToSkip($writeResult, $context)) {
            return null;
        }

        return [
            'id' => $writeResult->getPrimaryKey(),
            'name' => $payload['name'] ?? null,
            'operation' => $writeResult->getOperation(),
            'entityName' => $writeResult->getEntityName(),
            'timestamp' => (new DateTime())->format(DateTime::ATOM),
        ];
    }

    private function isAllowedToSkip(EntityWriteResult $writeResult, Context $context): bool
    {
        return $this->containsTranslationInsertion($writeResult) || !$this->containsChangedData($writeResult, $context);
    }

    private function fetchBlogIdByAffectedEntity(AffectedEntity $affectedEntity, Context $context): string
    {
        $entityName = $affectedEntity->getName();

        $id = $affectedEntity->getId();

        switch ($entityName) {
            case EEComBlogDefinition::ENTITY_NAME:
                $criteria = CriteriaFactory::withIds($id);
                break;
            /*case EEComBlogCategoryDefinition::ENTITY_NAME:
                $criteria = CriteriaFactory::forPageByBlockId($id);
                break;
            case EEComBlogVisibilityDefinition::ENTITY_NAME:
                $criteria = CriteriaFactory::forPageBySlotId($id);
                break;
            case EEComBlogTranslationDefinition::ENTITY_NAME:
                $criteria = CriteriaFactory::forPageBySectionId($id);
                break;*/
        }


        return $this->pageRepository
            ->search($criteria, $context)
            ->first()
            ->getId();
    }

    private function mergeActivityDetails(array $newDetails, ?array $activityDetails): array
    {
        if (!$activityDetails) {
            return $newDetails;
        }

        return array_merge(array_reverse($newDetails), $activityDetails);
    }

    private function fetchDraftActivity(string $blogId, ?string $draftVersion, Context $context): ?ArrayEntity
    {
        $criteria = new Criteria();
        $criteria->setLimit(1);
        $criteria->addFilter(
            new MultiFilter(MultiFilter::CONNECTION_AND, [
                new EqualsFilter('eecomBlogId', $blogId),
                new EqualsFilter('draftVersion', $draftVersion),
            ])
        );

        $criteria->addSorting(new FieldSorting('createdAt', FieldSorting::DESCENDING));

        return $this->versionControlBlogGateway->searchActivities($criteria, $context->createWithVersionId(Defaults::LIVE_VERSION))
            ->first();
    }

    public function containsChangedData(EntityWriteResult $writeResult, Context $context): bool
    {
        if ($writeResult->getOperation() !== EntityWriteResult::OPERATION_UPDATE) {
            return true;
        }

        return UpdateChangeContextExtension::extract($context)->hasChanges($writeResult);
    }

    private function containsTranslationInsertion(EntityWriteResult $writeResult): bool
    {
        return $writeResult->getOperation() === EntityWriteResult::OPERATION_INSERT
            && WriteResultExtractor::isTranslation($writeResult);
    }

    private function determineDraftVersion(Context $context): ?string
    {
        if ($context->getVersionId() === Defaults::LIVE_VERSION) {
            return null;
        }

        $draftVersion = $context->getVersionId();

        $drafts = $this->versionControlBlogGateway
            ->searchDrafts(
                CriteriaFactory::forDraftWithVersion($draftVersion),
                $context->createWithVersionId(Defaults::LIVE_VERSION)
            );

        if (!$drafts->count()) {
            throw new NoDraftFound();
        }

        return $draftVersion;
    }

    /**
     * @param EntityWriteResult[] $writeResults
     */
    private function extractDetails(array $writeResults, Context $context): array
    {
        $pageIdToDetailMap = [];
        foreach ($writeResults as $writeResult) {
            $affectedEntity = WriteResultExtractor::extractAffectedEntity($writeResult);
            $blogId = $this->pageIdMap[$this->getIdMapKey($affectedEntity)];

            $details = $this->extractDetailsFromWriteResults($writeResult, $context);

            if (!$details) {
                continue;
            }

            $this->updateDetailsByAffectedEntity($details, $affectedEntity);

            if (!isset($pageIdToDetailMap[$blogId])) {
                $pageIdToDetailMap[$blogId] = [];
            }

            $pageIdToDetailMap[$blogId][] = $details;
        }

        return $pageIdToDetailMap;
    }

    private function updateDetailsByAffectedEntity(array &$details, AffectedEntity $affectedEntity): void
    {
        if ($details['id'] === $affectedEntity->getId()) {
            return;
        }

        $details['id'] = $affectedEntity->getId();
        $details['entityName'] = $affectedEntity->getName();
    }

    private function loadActivities(array $pageIdToDetailMap, ?string $draftVersion, Context $context): array
    {
        $pageIdToActivityMap = [];
        foreach ($pageIdToDetailMap as $blogId => $null) {
            $pageIdToActivityMap[$blogId] = $this
                ->fetchDraftActivity($blogId, $draftVersion, $context);
        }

        return $pageIdToActivityMap;
    }

    private function writeActivityDetails(
        array         $pageIdToDetailMap,
        array         $pageIdToActivityMap,
        ContextSource $source,
        ?string       $draftVersion,
        Context       $context
    ): void
    {
        foreach ($pageIdToDetailMap as $blogId => $details) {
            $activity = $pageIdToActivityMap[$blogId];

            if (!$activity || $activity['userId'] !== $source->getUserId()) {
                $this->createNewActivity($blogId, $draftVersion, $details, $context);

                continue;
            }

            $this->updateExistingActivity($activity, $details, $context);
        }
    }

    private function getIdMapKey(AffectedEntity $affectedEntity): string
    {
        return $affectedEntity->getName() . $affectedEntity->getId();
    }

    /**
     * @param EntityWriteResult[] $writeResults
     */
    private function filterOnlyRememberedWriteResults(array $writeResults): array
    {
        $filteredWriteResults = [];

        foreach ($writeResults as $writeResult) {
            $affectedEntity = WriteResultExtractor::extractAffectedEntity($writeResult);
            $key = $this->getIdMapKey($affectedEntity);

            if (!isset($this->pageIdMap[$key])) {
                continue;
            }

            $filteredWriteResults[] = $writeResult;
        }

        return $filteredWriteResults;
    }

    private function fetchDraftName(string $blogId, ?string $draftVersion, Context $context): string
    {
        if (!$draftVersion) {
            return $this->fetchOriginalPageName($blogId, $context);
        }

        $criteria = CriteriaFactory::forActivityWithPageAndVersion($blogId, $draftVersion);

        $activity = $this->versionControlBlogGateway
            ->searchActivities($criteria, $context)
            ->first();

        if (!$activity) {
            return $this->fetchOriginalPageName($blogId, $context);
        }

        return $activity->get('name');
    }

    private function fetchOriginalPageName(string $blogId, Context $context): string
    {
        return $this->versionControlBlogGateway
            ->fetchInheritedDraftData($blogId, $context)['name'];
    }
}

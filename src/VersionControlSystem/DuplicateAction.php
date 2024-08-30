<?php declare(strict_types=1);

namespace EECom\EEComBlog\VersionControlSystem;

use Shopware\Core\Content\Cms\CmsPageDefinition;
use Shopware\Core\Content\Cms\CmsPageEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Core\Framework\Util\Random;
use EECom\EEComBlog\VersionControlSystem\Internal\CommonService;
use EECom\EEComBlog\VersionControlSystem\Internal\CriteriaFactory;
use EECom\EEComBlog\VersionControlSystem\Internal\VersionControlBlogGateway;
use EECom\EEComBlog\VersionControlSystem\Internal\VersionControlService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class DuplicateAction
{
    /**
     * @var VersionControlService
     */
    private VersionControlService $vcsService;

    /**
     * @var CommonService
     */
    private CommonService $commonService;

    /**
     * @var VersionControlBlogGateway
     */
    private VersionControlBlogGateway $blogGateway;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogRepository;

    public function __construct(
        VersionControlService     $vcsService,
        CommonService             $commonService,
        VersionControlBlogGateway $blogGateway,
        EntityRepositoryInterface $blogRepository
    )
    {
        $this->vcsService = $vcsService;
        $this->commonService = $commonService;
        $this->blogGateway = $blogGateway;
        $this->blogRepository = $blogRepository;
    }

    /**
     * @Route("/api/_action/eecom_blog/{blogId}/duplicate/{draftVersion}", name="api.action.eecom_blog.version_control_system.duplicate", methods={"POST"})
     */
    public function invoke(
        string  $blogId,
        string  $draftVersion,
        Context $context
    ): JsonResponse
    {
        return new JsonResponse($this->duplicate($blogId, $draftVersion, $context));
    }

    public function duplicate(string $blogId, string $draftVersion, Context $context): string
    {
        $userId = $this->commonService
            ->extractUserId($context);

        $originalDraft = $this->commonService
            ->requireDraftsByPageIdAndVersion($blogId, $draftVersion, $context)
            ->first();

        $newVersionContext = $this->vcsService
            ->duplicate($blogId, CmsPageDefinition::ENTITY_NAME, $context->createWithVersionId($draftVersion));

        $newVersionPage = $this->blogRepository
            ->search(CriteriaFactory::withIds($blogId), $newVersionContext)
            ->first();

        $context->scope(Context::SYSTEM_SCOPE, function ($systemContext) use ($newVersionPage, $originalDraft, $userId): void {
            $this->createDraft($newVersionPage, $originalDraft, $userId, $systemContext);
            $this->logActivity($newVersionPage, $originalDraft->get('name'), $userId, $systemContext);
        });

        return $newVersionContext->getVersionId();
    }

    private function createDraft(
        CmsPageEntity $newVersionPage,
        ArrayEntity   $originalDraft,
        ?string       $userId,
        Context       $context
    ): void
    {
        $this->cmsGateway->createDrafts([[
            'name' => $originalDraft->get('name'),
            'previewMediaId' => $originalDraft->get('previewMediaId'),
            'pageId' => $newVersionPage->getId(),
            'ownerId' => $userId,
            'draftVersion' => $newVersionPage->getVersionId(),
            'deepLinkCode' => Random::getBase64UrlString(32),
        ]], $context);
    }

    private function logActivity(CmsPageEntity $newVersionPage, string $name, ?string $userId, Context $context): void
    {
        $this->cmsGateway->createActivities([[
            'pageId' => $newVersionPage->getId(),
            'draftVersion' => $newVersionPage->getVersionId(),
            'userId' => $userId,
            'name' => $name,
        ]], $context);
    }
}

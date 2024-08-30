<?php declare(strict_types=1);

namespace EECom\EEComBlog\VersionControlSystem;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use EECom\EEComBlog\VersionControlSystem\Internal\VersionControlBlogGateway;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use EECom\EEComBlog\VersionControlSystem\Data\EEComBlogDraftCollection;
use EECom\EEComBlog\VersionControlSystem\Internal\CommonService;
use EECom\EEComBlog\VersionControlSystem\Internal\CriteriaFactory;
use EECom\EEComBlog\VersionControlSystem\Internal\VersionControlService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class UpdateFromLiveVersionAction
{
    private const DRAFT_VERSION_KEY = 'draftVersion';

    private VersionControlService $versionControlService;

    private CommonService $commonService;

    private VersionControlBlogGateway $blogGateway;

    public function __construct(
        VersionControlService $versionControlService,
        CommonService $commonService,
        VersionControlBlogGateway $blogGateway
    ) {
        $this->versionControlService = $versionControlService;
        $this->commonService = $commonService;
        $this->blogGateway = $blogGateway;
    }

    /**
     * @Route("/api/_action/eecom_blog/{blogId}/updateFromLiveVersion/{draftVersion}", name="api.action.cms_page.version_control_system.updateFromLiveVersion", methods={"POST"})
     */
    public function invoke(
        string $blogId,
        string $draftVersion,
        Context $context
    ): JsonResponse {
        return new JsonResponse($this->updateFromLiveVersion($blogId, $draftVersion, $context));
    }

    public function updateFromLiveVersion(
        string $blogId,
        string $draftVersion,
        Context $context
    ): string {
        $drafts = $this->commonService
            ->requireDraftsByBlogIdAndVersion($blogId, $draftVersion, $context);

        $versionContext = $context->createWithVersionId($draftVersion);

        $newVersionId = $this->versionControlService
            ->updateFromLiveVersion($blogId, EEComBlogDefinition::ENTITY_NAME, $versionContext);

        $context->scope(Context::SYSTEM_SCOPE, function ($systemContext) use ($drafts, $newVersionId): void {
            $this->updateDrafts($drafts, $newVersionId, $systemContext);
            $this->updateActivities($newVersionId, $systemContext);
        });

        return $newVersionId;
    }

    private function updateDrafts(
        EEComBlogDraftCollection $drafts,
        string $newVersionId,
        Context $context
    ): void {
        $this->blogGateway
            ->updateDrafts($drafts->forUpdate([self::DRAFT_VERSION_KEY => $newVersionId]), $context);
    }

    private function updateActivities(
        string $newVersionId,
        Context $context
    ): void {
        $updateData = $this->blogGateway
            ->searchActivities(CriteriaFactory::forDraftWithVersion($newVersionId), $context);

        $this->blogGateway
            ->updateActivities($updateData->forUpdate([self::DRAFT_VERSION_KEY => $newVersionId]), $context);
    }
}

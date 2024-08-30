<?php declare(strict_types=1);

namespace EECom\EEComBlog\VersionControlSystem;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use EECom\EEComBlog\VersionControlSystem\Internal\VersionControlBlogGateway;
use Shopware\Core\Content\Cms\CmsPageDefinition;
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
class ReleaseAsNewAction
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

    public function __construct(
        VersionControlService     $vcsService,
        CommonService             $commonService,
        VersionControlBlogGateway $blogGateway
    )
    {
        $this->vcsService = $vcsService;
        $this->commonService = $commonService;
        $this->blogGateway = $blogGateway;
    }

    /**
     * @Route("/api/_action/eecon_blog/{blogId}/releaseAsNew/{draftVersion}", name="api.action.cms_page.version_control_system.releaseAsNew", methods={"POST"})
     */
    public function invoke(
        string  $blogId,
        string  $draftVersion,
        Context $context
    ): JsonResponse
    {
        return new JsonResponse($this->releaseAsNew($blogId, $draftVersion, $context));
    }

    public function releaseAsNew(
        string  $blogId,
        string  $draftVersion,
        Context $context
    ): string
    {
        $drafts = $this->commonService
            ->requireDraftsByBlogIdAndVersion($blogId, $draftVersion, $context);
        $versionContext = $context->createWithVersionId($draftVersion);

        $newPageId = $this->vcsService
            ->releaseAsNew($blogId, EEComBlogDefinition::ENTITY_NAME, $versionContext);

        $context->scope(Context::SYSTEM_SCOPE, function (Context $context) use ($drafts, $draftVersion): void {
            $this->removeDrafts($drafts, $context);
            $this->setActivitiesReleased($draftVersion, $context);
        });

        return $newPageId;
    }

    private function removeDrafts(EEComBlogDraftCollection $drafts, Context $context): void
    {
        $this->blogGateway->deleteDrafts($drafts->asDelete(), $context);
    }

    private function setActivitiesReleased(string $draftVersion, Context $context): void
    {
        $criteria = CriteriaFactory::forDraftWithVersion($draftVersion);

        $updateData = $this->blogGateway
            ->searchActivities($criteria, $context);

        $this->blogGateway
            ->updateActivities($updateData->forUpdate(['isReleasedAsNew' => true]), $context);
    }
}

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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class MergeAction
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
    private VersionControlBlogGateway $cmsGateway;

    public function __construct(
        VersionControlService     $vcsService,
        CommonService             $commonService,
        VersionControlBlogGateway $cmsGateway
    )
    {
        $this->vcsService = $vcsService;
        $this->commonService = $commonService;
        $this->cmsGateway = $cmsGateway;
    }

    /**
     * @Route("/api/_action/eecom_blog/{blogId}/merge/{draftVersion}", name="api.action.eecom_blog.version_control_system.merge", methods={"POST"})
     */
    public function invoke(
        string  $blogId,
        string  $draftVersion,
        Context $context
    ): JsonResponse
    {
        $this->merge($blogId, $draftVersion, $context);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    public function merge(
        string  $blogId,
        string  $draftVersion,
        Context $context
    ): void
    {
        $drafts = $this->commonService
            ->requireDraftsByBlogIdAndVersion($blogId, $draftVersion, $context);

        $this->vcsService
            ->merge($draftVersion, EEComBlogDefinition::ENTITY_NAME, $context);

        $context->scope(Context::SYSTEM_SCOPE, function (Context $context) use ($drafts, $draftVersion): void {
            $this->removeDrafts($drafts, $context);
            $this->setActivitiesMerged($draftVersion, $context);
        });
    }

    private function removeDrafts(EEComBlogDraftCollection $drafts, Context $context): void
    {
        $this->cmsGateway->deleteDrafts($drafts->asDelete(), $context);
    }

    private function setActivitiesMerged(string $draftVersion, Context $context): void
    {
        $criteria = CriteriaFactory::forDraftWithVersion($draftVersion);

        $updateData = $this->cmsGateway
            ->searchActivities($criteria, $context);

        $this->cmsGateway
            ->updateActivities($updateData->forUpdate(['isMerged' => true]), $context);
    }
}

<?php declare(strict_types=1);

namespace EECom\EEComBlog\VersionControlSystem;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use EECom\EEComBlog\VersionControlSystem\Data\EEComBlogDraftCollection;
use EECom\EEComBlog\VersionControlSystem\Internal\CommonService;
use EECom\EEComBlog\VersionControlSystem\Internal\CriteriaFactory;
use EECom\EEComBlog\VersionControlSystem\Internal\VersionControlBlogGateway;
use EECom\EEComBlog\VersionControlSystem\Internal\VersionControlService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class DiscardAction
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
     * @Route("/api/_action/eecom_blog/{blogId}/discard/{draftVersion}", name="api.action.eecom_blog.version_control_system.discard", methods={"POST"})
     */
    public function invoke(
        string  $blogId,
        string  $draftVersion,
        Context $context
    ): JsonResponse
    {

        $this->discard($blogId, $draftVersion, $context);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    public function discard(
        string  $blogId,
        string  $draftVersion,
        Context $context
    ): void
    {

        $drafts = $this->commonService
            ->requireDraftsByBlogIdAndVersion($blogId, $draftVersion, $context);

        $this->vcsService
            ->discard(
                $blogId,
                EEComBlogDefinition::ENTITY_NAME,
                $context->createWithVersionId($draftVersion)
            );


        $context->scope(Context::SYSTEM_SCOPE, function (Context $context) use ($drafts, $draftVersion): void {
            $this->removeDrafts($drafts, $context);
            $this->setActivityDiscarded($draftVersion, $context);
        });
    }

    private function removeDrafts(EEComBlogDraftCollection $drafts, Context $context): void
    {
        $this->blogGateway->deleteDrafts($drafts->asDelete(), $context);
    }

    private function setActivityDiscarded(string $draftVersion, Context $context): void
    {
        $criteria = CriteriaFactory::forDraftWithVersion($draftVersion);

        $activities = $this->blogGateway
            ->searchActivities($criteria, $context);

        $this->blogGateway
            ->updateActivities($activities->forUpdate(['isDiscarded' => true]), $context);
    }
}

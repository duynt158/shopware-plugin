<?php declare(strict_types=1);

namespace EECom\EEComBlog\VersionControlSystem;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use EECom\EEComBlog\VersionControlSystem\Internal\CommonService;
use EECom\EEComBlog\VersionControlSystem\Internal\VersionControlBlogGateway;
use EECom\EEComBlog\VersionControlSystem\Internal\VersionControlService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use function array_merge;

/**
 * @RouteScope(scopes={"api"})
 */
class DraftAction
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
     * @Route("/api/_action/eecom_blog/{blogId}/draft", name="api.action.eecom_blog.version_control_system.draft", methods={"POST"})
     */
    public function invoke(
        string         $blogId,
        RequestDataBag $request,
        Context        $context
    ): JsonResponse
    {

        return new JsonResponse($this->draft($blogId, $request->get('name'), $context));
    }

    public function draft(
        string  $blogId,
        ?string $name,
        Context $context
    ): string
    {

        $userId = $this->commonService
            ->extractUserId($context);

        $versionId = $this->branchPage($blogId, $context);

        $context->scope(Context::SYSTEM_SCOPE, function ($systemContext) use ($blogId, $versionId, $userId, $name): void {
            $inheritedDraftData = $this->blogGateway
                ->fetchInheritedDraftData($blogId, $systemContext);

            if ($name) {
                $inheritedDraftData['name'] = $name;
            }

            $this->addDraft($inheritedDraftData, $versionId, $userId, $systemContext);
            $this->logActivity($inheritedDraftData, $versionId, $userId, $systemContext);
        });

        return $versionId;
    }

    private function branchPage(string $blogId, Context $context): string
    {

        return $this->vcsService
            ->branch($blogId, EEComBlogDefinition::ENTITY_NAME, $context)->getVersionId();
    }

    private function addDraft(array $inheritedDraftData, string $versionId, ?string $userId, Context $context): void
    {

        $draftData = array_merge([
            'ownerId' => $userId,
            'draftVersion' => $versionId,
            'deepLinkCode' => Random::getBase64UrlString(32),
        ], $inheritedDraftData);

        $this->blogGateway->createDrafts([$draftData], $context);
    }

    private function logActivity(array $inheritedDraftData, string $versionId, ?string $userId, Context $context): void
    {


        $activityData = array_merge([
            'draftVersion' => $versionId,
            'userId' => $userId,
        ], $inheritedDraftData);

        $this->blogGateway->createActivities([$activityData], $context);

    }
}

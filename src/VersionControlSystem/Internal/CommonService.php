<?php declare(strict_types=1);

namespace EECom\EEComBlog\VersionControlSystem\Internal;

use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Context;
use EECom\EEComBlog\VersionControlSystem\Data\EEComBlogDraftCollection;
use EECom\EEComBlog\VersionControlSystem\Exception\NoDraftFound;
use EECom\EEComBlog\VersionControlSystem\Internal\CriteriaFactory;
use EECom\EEComBlog\VersionControlSystem\Internal\VersionControlBlogGateway;

class CommonService
{
    /**
     * @var VersionControlBlogGateway
     */
    private $blogGateway;

    public function __construct(
        VersionControlBlogGateway $blogGateway
    )
    {
        $this->blogGateway = $blogGateway;
    }

    public function requireDraftsByBlogIdAndVersion(string $blogId, string $draftVersion, Context $context): EEComBlogDraftCollection
    {
        $criteria = CriteriaFactory::forDraftWithBLogAndVersion($blogId, $draftVersion);

        $result = $this->blogGateway
            ->searchDrafts($criteria, $context);

        if (!$result->count()) {
            throw new NoDraftFound();
        }

        return $result;
    }

    public function extractUserId(Context $context): ?string
    {
        $source = $context->getSource();

        $userId = null;
        if ($source instanceof AdminApiSource) {
            $userId = $source->getUserId();
        }

        return $userId;
    }
}

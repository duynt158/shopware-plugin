<?php declare(strict_types=1);

namespace EECom\EEComBlog\VersionControlSystem\Internal;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\AndFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class CriteriaFactory
{
    public static function forDraftWithBLogAndVersion(string $blogId, string $draftVersion): Criteria
    {
        $criteria = new Criteria();
        $criteria->addFilter(new AndFilter([
            new EqualsFilter('eecomBlogId', $blogId),
        ]));

        return $criteria;
    }

    public static function forDraftWithVersion(string $draftVersion): Criteria
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('draftVersion', $draftVersion));

        return $criteria;
    }

    public static function withIds(string ...$ids): Criteria
    {
        return new Criteria($ids);
    }


    public static function forPageWithVersion(string $versionId): Criteria
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('versionId', $versionId));
        $criteria->setLimit(1);

        return $criteria;
    }

    public static function forActivityWithPageAndVersion(string $blogId, string $versionId): Criteria
    {
        $criteria = new Criteria();
        $criteria->addFilter(new AndFilter([
            new EqualsFilter('eecomBlogId', $blogId),
            new EqualsFilter('draftVersion', $versionId),
        ]));

        return $criteria;
    }
}

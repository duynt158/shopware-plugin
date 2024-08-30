<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\DataResolver;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\SalesChannel\Struct\ProductSliderStruct;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class EEComBlogTeaserBoxCmsElementResolver extends AbstractCmsElementResolver
{

    private const EECOM_BLOG_SLIDER_ENTITY_FALLBACK = 'eecom-blog-teaser-entity-fallback';
    private const STATIC_SEARCH_KEY = 'eecom-blog-teaser';
    private const FALLBACK_LIMIT = 50;

    public function getType(): string
    {
        return 'eecom-blog-teaser-box';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getFieldConfig();
        $collection = new CriteriaCollection();

        $blogs = $config->get('blogs');
        if ($blogs === null) {
            return null;
        }
        if ($blogs->isStatic() && $blogs->getValue()) {

            $criteria = new Criteria($blogs->getArrayValue());
            $criteria->addFilter(
                new EqualsFilter('active', true)
            );
        } else {
            $criteria = new Criteria();
            $criteria->addFilter(
                new EqualsFilter('active', true)
            );
            $criteria->setLimit($config->get('blogLimit')->getValue());
            $criteria->addSorting(
                new FieldSorting('publishedAt', FieldSorting::DESCENDING)
            );

        }

        $criteria->addAssociation('media');
        $criteria->addAssociation('user');
        $collection->add('eecom_blog_teaser', EEComBlogDefinition::class, $criteria);


        return $collection->all() ? $collection : null;

    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $slot->setData($result->get('eecom_blog_teaser'));
    }


}

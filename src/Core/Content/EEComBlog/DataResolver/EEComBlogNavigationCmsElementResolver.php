<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\DataResolver;


use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use EECom\EEComBlog\Core\Content\EEComBlogCategory\EEComBlogCategoryDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;

class EEComBlogNavigationCmsElementResolver extends AbstractCmsElementResolver
{

    public function getType(): string
    {
        return 'eecom-blog-category-navigation';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getFieldConfig();
        $collection = new CriteriaCollection();

        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('active', true)
        );
        $criteria->addSorting(new FieldSorting('position'));

        $collection->add('eecom_blog_navigation', EEComBlogCategoryDefinition::class, $criteria);

        return $collection->all() ? $collection : null;

    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {

        $slot->setData($result->get('eecom_blog_navigation'));
    }


}

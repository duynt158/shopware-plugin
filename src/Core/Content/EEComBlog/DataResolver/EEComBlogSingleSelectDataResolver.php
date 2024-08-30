<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\DataResolver;


use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class EEComBlogSingleSelectDataResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'eecom-single-blog';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        /* get the config from the element */
        $config = $slot->getFieldConfig();

        $criteria = new Criteria();

        $criteria->addFilter(
            new EqualsFilter('id', $config->get('blogEntry')->getValue())
        );

        $criteria->addAssociations(['user', 'media']);

        $criteriaCollection = new CriteriaCollection();

        $criteriaCollection->add(
            'eecom_single_blog',
            EEComBlogDefinition::class,
            $criteria
        );


        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        if ($result->get('eecom_single_blog')->first() !== null) {
            $slot->setData($result->get('eecom_single_blog')->first());
        }
    }
}

<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\Cms;


use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogEntity;
use EECom\EEComBlog\Core\Content\EEComBlog\SalesChannel\SalesChannelEEComBlogDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\FieldConfig;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

abstract class AbstractEEComBlogDetailCmsElementResolver extends AbstractCmsElementResolver
{
    abstract public function getType(): string;

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getFieldConfig();

        if ($resolverContext instanceof EntityResolverContext && $resolverContext->getDefinition() instanceof SalesChannelProductDefinition) {
            $productConfig = new FieldConfig('eecom_blog', FieldConfig::SOURCE_MAPPED, $resolverContext->getEntity()->get('id'));
            $config->add($productConfig);
        }

        $productConfig = $config->get('eecom_blog');
        if ($productConfig === null || $productConfig->isMapped() || $productConfig->getValue() === null) {
            return null;
        }


        $criteria = new Criteria();
        $criteriaCollection = new CriteriaCollection();
        $criteriaCollection->add('eecom_blog_' . $slot->getUniqueIdentifier(), SalesChannelEEComBlogDefinition::class, $criteria);

        return $criteriaCollection;
    }

    abstract public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void;

    protected function getSlotProduct(CmsSlotEntity $slot, ElementDataCollection $result, string $productId): ?EEComBlogEntity
    {
        $searchResult = $result->get('eecom_blog_' . $slot->getUniqueIdentifier());
        if ($searchResult === null) {
            return null;
        }


        /** @var EEComBlogEntity|null $eecomBlog */
        return $bestVariant ?? $searchResult->get($productId);
    }


}

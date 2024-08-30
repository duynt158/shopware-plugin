<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlogCategory;

use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlMapping;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteConfig;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use EECom\EEComBlog\Core\Content\EEComBlogCategory\EEComBlogCategoryDefinition;

class EEComBlogCategorySeoUrlRoute implements SeoUrlRouteInterface
{
    public const ROUTE_NAME = 'frontend.eecom.blog.category.detail.page';

    /**
     * @var EEComBlogCategoryDefinition
     */
    private $definition;


    /**
     * EEComBlogSeoUrlRoute constructor.
     * @param \EECom\EEComBlog\Core\Content\EEComBlogCategory\EEComBlogCategoryDefinition $definition
     */
    public function __construct(EEComBlogCategoryDefinition $definition)
    {
        $this->definition = $definition;
    }

    public function getConfig(): SeoUrlRouteConfig
    {
        return new SeoUrlRouteConfig(
            $this->definition,
            self::ROUTE_NAME,
            'eecom-blog-category/{{ blogCategory.id }}/{{ blogCategory.name|lower }}'
        );
    }

    public function prepareCriteria(Criteria $criteria): void
    {

        // TODO: Implement prepareCriteria() method.
    }

    public function getMapping(Entity $entity, ?SalesChannelEntity $salesChannel): SeoUrlMapping
    {


        return new SeoUrlMapping(
            $entity,
            ['id' => $entity->getId()],
            [
                'blogCategory' => $entity,
            ]
        );
    }
}

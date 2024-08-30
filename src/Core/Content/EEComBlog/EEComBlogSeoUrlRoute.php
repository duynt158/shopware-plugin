<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog;


use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlMapping;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteConfig;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;


class EEComBlogSeoUrlRoute implements SeoUrlRouteInterface
{
    public const ROUTE_NAME = 'frontend.eecom.blog.detail.page';

    /**
     * @var EEComBlogDefinition
     */
    private $definition;


    /**
     * EEComBlogSeoUrlRoute constructor.
     * @param EEComBlogDefinition $definition
     */
    public function __construct(EEComBlogDefinition $definition)
    {
        $this->definition = $definition;
    }

    public function getConfig(): SeoUrlRouteConfig
    {
        return new SeoUrlRouteConfig(
            $this->definition,
            self::ROUTE_NAME,
            'eecom-blog/{{ blog.id }}/{{ blog.name|lower }}'
        );
    }

    public function prepareCriteria(Criteria $criteria): void
    {

        $criteria->addAssociation('categories');
    }

    public function getMapping(Entity $entity, ?SalesChannelEntity $salesChannel): SeoUrlMapping
    {


        return new SeoUrlMapping(
            $entity,
            ['id' => $entity->getId()],
            [
                'blog' => $entity,
            ]
        );
    }
}

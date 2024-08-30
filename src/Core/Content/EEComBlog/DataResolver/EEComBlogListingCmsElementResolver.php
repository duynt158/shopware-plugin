<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\DataResolver;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Product\SalesChannel\Listing\Filter;
use Shopware\Core\Content\Product\SalesChannel\Listing\FilterCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\EntityAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;


class EEComBlogListingCmsElementResolver extends AbstractCmsElementResolver
{
    private EntityRepositoryInterface $blogCategoryRepository;

    public function __construct(EntityRepositoryInterface $blogCategoryRepository)
    {
        $this->blogCategoryRepository = $blogCategoryRepository;
    }


    public function getType(): string
    {
        return 'blog';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        /* get the config from the element */
        $config = $slot->getFieldConfig();

        $dateTime = new \DateTime();

        $criteria = new Criteria();

        $criteria->addFilter(
            new EqualsFilter('active', true),
            new RangeFilter('publishedAt', [RangeFilter::LTE => $dateTime->format(\DATE_ATOM)])
        );

        $criteria->addAssociations([
            'media',
            'user'
        ]);

        $criteria->addSorting(
            new FieldSorting('publishedAt', FieldSorting::DESCENDING)
        );

        if ($config->has('showType') && $config->get('showType')->getValue() === 'select') {
            $blogCategories = $config->get('categories') ? $config->get('categories')->getValue() : [];

            $criteria->addFilter(new EqualsAnyFilter('categories.id', $blogCategories));
        }


        $request = $resolverContext->getRequest();
        $limit = 1;
        if ($config->has('paginationCount') && $config->get('paginationCount')->getValue()) {
            $limit = (int)$config->get('paginationCount')->getValue();
        }

        $context = $resolverContext->getSalesChannelContext();

        $this->handlePagination($limit, $request, $criteria, $context);
        $this->handleFilters($request, $criteria, $context);

        $criteriaCollection = new CriteriaCollection();

        $criteriaCollection->add(
            'eecom_blog',
            EEComBlogDefinition::class,
            $criteria
        );


        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {

        $slot->setData($result->get('eecom_blog'));
    }

    private function handlePagination(int $limit, Request $request, Criteria $criteria, SalesChannelContext $context): void
    {
        $page = $this->getPage($request);
        if(!$request->query->get('eecomBlogCategories')) {
            $criteria->setLimit(30);
        }
        else{
            $criteria->setOffset(($page - 1) * $limit);
            $criteria->setLimit($limit);
            $criteria->setTotalCountMode(Criteria::TOTAL_COUNT_MODE_EXACT);
        }





    }

    private function getPage(Request $request): int
    {
        $page = $request->query->getInt('p', 1);

        if ($request->isMethod(Request::METHOD_POST)) {
            $page = $request->request->getInt('p', $page);
        }

        return $page <= 0 ? 1 : $page;
    }

    private function handleFilters(Request $request, Criteria $criteria, SalesChannelContext $context): void
    {
        $criteria->addAssociation('categories');

        $filters = $this->getFilters($request, $context);

        foreach ($filters as $filter) {
            if ($filter->isFiltered()) {
                $criteria->addPostFilter($filter->getFilter());
            }
        }

        $criteria->addExtension('filters', $filters);

    }

    private function getFilters(Request $request, SalesChannelContext $context): FilterCollection
    {
        $filters = new FilterCollection();

        $filters->add($this->getCategoriesFilter($request,$context));


        return $filters;
    }

    private function getCategoriesFilter(Request $request, SalesChannelContext $context): Filter
    {

        $ids = $this->getFilterByCustomIds('eecomBlogCategories', $request);

        return new Filter(
            'eecomBlogCategories',
            !empty($ids),
            [
                new EntityAggregation('categories', 'categories.id', 'eecom_blog_category'),
            ],
            new EqualsAnyFilter('categories.id', $ids),
            $ids
        );
    }

    private function getFilterByCustomIds(string $input, Request $request): array
    {

        $ids = $request->query->get($input, '');

        if ($request->isMethod(Request::METHOD_POST)) {
            $ids = $request->request->get($input, '');
        }

        $ids = explode('|', $ids);

        return array_filter($ids);
    }


}

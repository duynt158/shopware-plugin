<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlogCategory\SalesChannel;


use EECom\EEComBlog\Core\Content\EEComBlogCategory\EEComBlogCategoryDefinition;
use EECom\EEComBlog\Core\Content\EEComBlogCategory\EEComBlogCategoryEntity;
use OpenApi\Annotations as OA;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Content\Cms\SalesChannel\SalesChannelCmsPageLoaderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Routing\Annotation\Since;
use Shopware\Core\Framework\Routing\Annotation\Entity;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"store-api"})
 */
class EEComBlogCategoryRoute extends AbstractEEComBlogCategoryRoute
{

    /**
     * @var EntityRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var SalesChannelCmsPageLoaderInterface
     */
    private $cmsPageLoader;

    /**
     * @var EEComBlogCategoryDefinition
     */
    private $categoryDefinition;

    public function __construct(
        EntityRepositoryInterface $categoryRepository,
        SalesChannelCmsPageLoaderInterface $cmsPageLoader,
        EEComBlogCategoryDefinition $categoryDefinition
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->cmsPageLoader = $cmsPageLoader;
        $this->categoryDefinition = $categoryDefinition;
    }

    public function getDecorated(): AbstractEEComBlogCategoryRoute
    {
        throw new DecorationPatternException(self::class);
    }

    /**
     * @Since("6.2.0.0")
     * @Entity("eecom_blog_category")
     * @OA\Post(
     *     path="/eecom_blog_category/{categoryId}",
     *     summary="Fetch a single category",
     *     description="This endpoint returns information about the category, as well as a fully resolved (hydrated with mapping values) CMS page, if one is assigned to the category. You can pass slots which should be resolved exclusively.",
     *     operationId="readCategory",
     *     tags={"Store API", "eecomBlog Category"},
     *
     *     @OA\Parameter(
     *         name="categoryId",
     *         description="Identifier of the category to be fetched",
     *         @OA\Schema(type="string", pattern="^[0-9a-f]{32}$"),
     *         in="path",
     *         required=true
     *     )
     * )
     *
     * @Route("/store-api/eecom_blog_category/{categoryId}", name="store-api.eecom.blog.category.detail", methods={"GET","POST"})
     */
    public function load(string $categoryId, Request $request, SalesChannelContext $context, Criteria $criteria): EEComBlogCategoryRouteResponse
    {

        if (!$categoryId) {
            throw new MissingRequestParameterException('eecom-blog-category', '/eecom-blog-category');
        }

        $criteria->setIds([$categoryId]);

        /** @var EEComBlogCategoryEntity $entry */
        $result = $this->categoryRepository->search($criteria, $context->getContext())->getEntities()->first();


        if (!$result) {
            throw new PageNotFoundException($categoryId);
        }

        return new EEComBlogCategoryRouteResponse($result);



    }



}

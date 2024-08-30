<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\SalesChannel\Suggest;

use EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogVisibility\EEComBlogVisibilityDefinition;
use EECom\EEComBlog\Core\Content\EEComBlog\SalesChannel\Listing\EEComBlogListingLoader;
use EECom\EEComBlog\Core\Content\EEComBlog\SearchKeyword\EEComBlogSearchBuilderInterface;
use OpenApi\Annotations as OA;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;
use Shopware\Core\Content\Product\Events\ProductSuggestCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductSuggestResultEvent;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingLoader;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Shopware\Core\Content\Product\SalesChannel\ProductAvailableFilter;
use Shopware\Core\Content\Product\SearchKeyword\ProductSearchBuilderInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\Framework\Routing\Annotation\Entity;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Routing\Annotation\Since;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @RouteScope(scopes={"store-api"})
 */
class EEComBlogSuggestRoute extends AbstractEEComBlogSuggestRoute
{
    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @var EEComBlogSearchBuilderInterface
     */
    private EEComBlogSearchBuilderInterface $searchBuilder;

    /**
     * @var EEComBlogListingLoader
     */
    private EEComBlogListingLoader $productListingLoader;

    public function __construct(
        EEComBlogSearchBuilderInterface $searchBuilder,
        EventDispatcherInterface        $eventDispatcher,
        EEComBlogListingLoader          $productListingLoader
    )
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->searchBuilder = $searchBuilder;
        $this->productListingLoader = $productListingLoader;
    }

    public function getDecorated(): AbstractEEComBlogSuggestRoute
    {
        throw new DecorationPatternException(self::class);
    }

    /**
     * @Since("6.2.0.0")
     * @Entity("eecom_blog")
     * @OA\Post(
     *      path="/search-suggest",
     *      summary="Search for products (suggest)",
     *      description="Can be used to implement search previews or suggestion listings, that don’t require any interaction.",
     *      operationId="searchSuggest",
     *      tags={"Store API","EECom Blog"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ProductListingFlags"),
     *                  @OA\Schema(type="object",
     *                      required={
     *                          "search"
     *                      },
     *                      @OA\Property(
     *                          property="search",
     *                          description="Using the search parameter, the server performs a text search on all records based on their data model and weighting as defined in the entity definition using the SearchRanking flag.",
     *                          type="string"
     *                      )
     *                  )
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Returns a product listing containing all products and additional fields.

    Note: Aggregations, currentFilters and availableSortings are empty in this response. If you need them to display a listing, use the /search route instead.",
     *          @OA\JsonContent(ref="#/components/schemas/ProductListingResult")
     *     )
     * )
     * @Route("/store-api/search-suggest", name="store-api.search.suggest", methods={"POST"})
     */
    public function load(Request $request, SalesChannelContext $context, Criteria $criteria): EEComBlogSuggestRouteResponse
    {

        if (!$request->get('search')) {
            throw new MissingRequestParameterException('search');
        }

        $criteria->addFilter(
            new ProductAvailableFilter($context->getSalesChannel()->getId(), EEComBlogVisibilityDefinition::VISIBILITY_SEARCH)
        );

        $context->getContext()->addState(Context::STATE_ELASTICSEARCH_AWARE);

        $this->searchBuilder->build($request, $criteria, $context);

        $this->eventDispatcher->dispatch(
            new ProductSuggestCriteriaEvent($request, $criteria, $context),
            ProductEvents::PRODUCT_SUGGEST_CRITERIA
        );

        $result = $this->productListingLoader->load($criteria, $context);

        $result = ProductListingResult::createFrom($result);

        $this->eventDispatcher->dispatch(
            new ProductSuggestResultEvent($request, $result, $context),
            ProductEvents::PRODUCT_SUGGEST_RESULT
        );

        return new EEComBlogSuggestRouteResponse($result);
    }
}

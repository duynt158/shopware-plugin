<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\SalesChannel\Detail;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogEntity;
use OpenApi\Annotations as OA;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Content\Cms\SalesChannel\SalesChannelCmsPageLoaderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\Framework\Routing\Annotation\Entity;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Routing\Annotation\Since;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"store-api"})
 */
class BlogDetailRoute extends AbstractBlogDetailRoute
{
    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $eecomBlogRepository;

    /**
     * @var SystemConfigService
     */
    private SystemConfigService $config;


    /**
     * @var SalesChannelCmsPageLoaderInterface
     */
    private SalesChannelCmsPageLoaderInterface $cmsPageLoader;

    /**
     * @var EEComBlogDefinition
     */
    private EEComBlogDefinition $EEComBlogDefinition;

    public function __construct(
        EntityRepositoryInterface $eecomBlogRepository,
        SystemConfigService $config,
        SalesChannelCmsPageLoaderInterface $cmsPageLoader,
        EEComBlogDefinition $EEComBlogDefinition
    ) {
        $this->eecomBlogRepository = $eecomBlogRepository;
        $this->config = $config;
        $this->cmsPageLoader = $cmsPageLoader;
        $this->EEComBlogDefinition = $EEComBlogDefinition;
    }

    public function getDecorated(): AbstractBlogDetailRoute
    {
        throw new DecorationPatternException(self::class);
    }

    /**
     * @Since("6.3.2.0")
     * @Entity("eecom_blog")
     * @OA\Post(
     *      path="/eecom_blog/{blogId}",
     *      summary="Fetch a single blog",
     *      description="This route is used to load a single blog with the corresponding details. In addition to loading the data.",
     *      operationId="readBlogDetail",
     *      tags={"Store API","Blog"},
     *      @OA\Parameter(
     *          name="blogid",
     *          description="Blog ID",
     *          @OA\Schema(type="string"),
     *          in="path",
     *          required=true
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Blog information",
     *          @OA\JsonContent(ref="#/components/schemas/ProductDetailResponse")
     *     )
     * )
     * @Route("/store-api/eecom_blog/{blogId}", name="store-api.eecom.blog.detail", methods={"POST"})
     */
    public function load(string $blogId, Request $request, SalesChannelContext $context, Criteria $criteria): BlogDetailRouteResponse
    {
        if (!$blogId) {
            throw new MissingRequestParameterException('eecom-blog', '/eecom-blog');
        }

        $this->addFilters($context, $criteria);
        $criteria->setIds([$blogId]);
        $eecomBlog = $this->findBlog($blogId,$criteria, $context);

        $cmsPageId = $this->config->get('eecomBlog.config.cmsBlogDetailPage');
        if($eecomBlog->getCmsPageId())
        {
            $cmsPageId = $eecomBlog->getCmsPageId();
        }

        if ($cmsPageId) {
            $resolverContext = new EntityResolverContext($context, $request, $this->EEComBlogDefinition, $eecomBlog);
            $criteria = new Criteria([$cmsPageId]);
            $pages = $this->cmsPageLoader->load(
                $request,
                $criteria,
                $context,
                $eecomBlog->getTranslation('slotConfig'),
                $resolverContext
            );

            if ($page = $pages->first()) {
                $eecomBlog->setCmsPage($page);
            }
        }


        return new BlogDetailRouteResponse($eecomBlog);
    }
    private function addFilters(SalesChannelContext $context, Criteria $criteria): void
    {

    }
    private function findBlog(string $id,Criteria $criteria, SalesChannelContext $context): EEComBlogEntity
    {

        return $this->eecomBlogRepository->search($criteria, $context->getContext())->first();
    }


}

<?php declare(strict_types=1);

namespace EECom\EEComBlog\Controller;


use EECom\EEComBlog\Storefront\Page\EEComBlog\EEComBlogPageLoader;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Framework\Cache\Annotation\HttpCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @RouteScope(scopes={"storefront"})
 */
class EEComBlogController extends StorefrontController
{

    /**
     * @var EEComBlogPageLoader
     */
    protected EEComBlogPageLoader $eeComBlogPageLoader;

    public function __construct(
        EEComBlogPageLoader $eeComBlogPageLoader
    )
    {
        $this->eeComBlogPageLoader = $eeComBlogPageLoader;
    }

    /**
     * @HttpCache()
     * @Route("/eecom_blog/{id}", name="frontend.eecom.blog.detail.page", methods={"GET"})
     */
    public function detailAction(string $id, Request $request, SalesChannelContext $context): Response
    {

        $page = $this->eeComBlogPageLoader->load($request, $context);

        return $this->renderStorefront('@Storefront/storefront/page/content/index.html.twig', ['page' => $page]);


    }
}

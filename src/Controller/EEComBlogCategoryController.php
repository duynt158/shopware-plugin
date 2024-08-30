<?php declare(strict_types=1);

namespace EECom\EEComBlog\Controller;


use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use EECom\EEComBlog\Core\Content\EEComBlogCategory\EEComBlogCategoryEntity;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Category\SalesChannel\AbstractCategoryRoute;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Content\Cms\SalesChannel\SalesChannelCmsPageLoaderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Framework\Cache\Annotation\HttpCache;
use Shopware\Storefront\Page\GenericPageLoader;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Shopware\Storefront\Page\LandingPage\LandingPage;
use Shopware\Storefront\Page\LandingPage\LandingPageLoader;
use Shopware\Storefront\Page\Navigation\NavigationPage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class EEComBlogCategoryController extends StorefrontController{

    /**
     * @var GenericPageLoader
     */
    private $genericPageLoader;

    /**
     * @var LandingPageLoader
     */
    private $landingPageLoader;

    /**
     * @var SalesChannelCmsPageLoaderInterface
     */
    private SalesChannelCmsPageLoaderInterface $cmsPageLoader;

    /**
     * @var AbstractCategoryRoute
     */
    private $cmsPageRoute;

    public function __construct( GenericPageLoaderInterface $genericPageLoader,LandingPageLoader $landingPageLoader, SalesChannelCmsPageLoaderInterface $cmsPageLoader, AbstractCategoryRoute $cmsPageRoute ){

        $this->genericPageLoader = $genericPageLoader;
        $this->landingPageLoader = $landingPageLoader;
        $this->cmsPageLoader = $cmsPageLoader;
        $this->cmsPageRoute = $cmsPageRoute;
    }

    /**
     * @Route("/eecom_blog_category/{id}", name="frontend.eecom.blog.category.detail.page", methods={"GET"})
     */
    public function detailAction(?string $categoryId, Request $request, SalesChannelContext $context): Response
    {
        if (!$categoryId){
            $categoryId = $request->get( 'id' );
        }
        if (!$categoryId) {
            throw new PageNotFoundException('blog_category');
        }

        $request->query->set('eecomBlogCategories',$categoryId);

        /** @var EntityRepositoryInterface $blogCategoryRepository */
        $blogCategoryRepository = $this->container->get('eecom_blog_category.repository');

        $criteria = new Criteria([$categoryId]);
        $criteria->addAssociation('seoUrls');

        $results = $blogCategoryRepository->search($criteria, $context->getContext())->getEntities();

        /** @var EEComBlogCategoryEntity $entry */
        $entry = $results->first();

        if (!$entry) {
            throw new PageNotFoundException($categoryId);
        }
        $page = $this->genericPageLoader->load($request, $context);
        $page = NavigationPage::createFrom($page);

        $criteria = new Criteria();
        $criteria->addAssociation('landingPages');
        $criteria->addAssociation('categories');
        $criteria->addFilter( new EqualsFilter('type','landingpage') );
        $criteria->getAssociation('categories')->addFilter( new EqualsFilter('type','page') );

        $criteria->addFilter( new MultiFilter(
            MultiFilter::CONNECTION_OR,
            [
                new EqualsFilter('sections.blocks.type', 'eecom-blog-listing'),
                new EqualsFilter('sections.blocks.type', 'eecom-blog-category-navigation'),
            ]
        ));
        $criteria->setLimit(1);

        $pages = $this->cmsPageLoader->load(
            $request,
            $criteria,
            $context
        );

        $cmsPage = $pages->first();

        $navigationId = '';
        if ( $cmsPage->getCategories()->count() ){
            $navigationId = $cmsPage->getCategories()->first()->getId();
        }

        if (!$navigationId && $cmsPage->getLandingPages()->count()){
            $navigationId = $cmsPage->getLandingPages()->first()->getId();
        }

        if (!$navigationId) {
            throw new PageNotFoundException('blog_cms_page');
        }

       // $page = $this->createLandingPage($request, $context, $navigationId);

        $page->setCmsPage($cmsPage);
        $metaInformation = $page->getMetaInformation();

        $page->setMetaInformation($metaInformation);
        $page->setNavigationId($navigationId);
        return $this->renderStorefront('@Storefront/storefront/page/content/index.html.twig', [
            'page' => $page,
            'entry' => $entry,
        ]);
    }

    private function createLandingPage(Request $request, SalesChannelContext $context, $navigationId): LandingPage
    {
        $page = $this->genericPageLoader->load($request, $context);
        /** @var LandingPage $page */
        $page = LandingPage::createFrom($page);

        $page->setNavigationId($navigationId);

        $category = $this->cmsPageRoute
            ->load($navigationId, $request, $context);

        $this->loadMetaData($category->getCategory(), $page);

        return $page;
    }

    private function loadMetaData(CategoryEntity $category, LandingPage $page): void
    {
        $metaInformation = $page->getMetaInformation();

        $metaDescription = $category->getTranslation('metaDescription')
            ?? $category->getTranslation('description');
        $metaInformation->setMetaDescription((string) $metaDescription);

        $metaTitle = $category->getTranslation('metaTitle')
            ?? $category->getTranslation('name');
        $metaInformation->setMetaTitle((string) $metaTitle);

        $metaInformation->setMetaKeywords((string) $category->getTranslation('keywords'));
    }
}

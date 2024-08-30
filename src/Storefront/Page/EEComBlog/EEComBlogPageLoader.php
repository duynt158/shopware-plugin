<?php

namespace EECom\EEComBlog\Storefront\Page\EEComBlog;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use Shopware\Core\Content\Cms\CmsPageEntity;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\SalesChannel\SalesChannelCmsPageLoaderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\GenericPageLoader;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class EEComBlogPageLoader
{
    /**
     * @var GenericPageLoader
     */
    private $genericLoader;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var EntityRepositoryInterface
     */
    private $eecomBlogRepository;
    /**
     * @var SalesChannelCmsPageLoaderInterface
     */
    private $cmsPageLoader;

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    /**
     * @var EEComBlogDefinition
     */
    private $EEComBlogDefinition;


    public function __construct(
        GenericPageLoader $genericLoader,
        EventDispatcherInterface $eventDispatcher,
        EntityRepositoryInterface $eecomBlogRepository,
        SalesChannelCmsPageLoaderInterface $cmsPageLoader,
        SystemConfigService $systemConfigService,
        EEComBlogDefinition $EEComBlogDefinition
    ) {
        $this->genericLoader = $genericLoader;
        $this->eventDispatcher = $eventDispatcher;
        $this->eecomBlogRepository = $eecomBlogRepository;
        $this->cmsPageLoader = $cmsPageLoader;
        $this->systemConfigService = $systemConfigService;
        $this->EEComBlogDefinition = $EEComBlogDefinition;
    }

    public function load(Request $request, SalesChannelContext $salesChannelContext): EEComBlogPage
    {
        $page = $this->genericLoader->load($request, $salesChannelContext);

        $page = EEComBlogPage::createFrom($page);

        $eecomBlogId = $request->attributes->get('id');

        if (!$eecomBlogId) {
            throw new MissingRequestParameterException('eecom-blog', '/eecom-blog');
        }
        $criteria = new Criteria([$eecomBlogId]);
        $criteria->addAssociation('media');
        $criteria->addAssociation('user');
        $criteria->addAssociation('tags');
        $criteria->addAssociation('cmsPage');
        $criteria->addAssociation('categories');
        $eecomBlog = $this->eecomBlogRepository->search($criteria, $salesChannelContext->getContext())->first();
        $cmsPageId = $this->systemConfigService->get('eecomBlog.config.cmsBlogDetailPage');
        if($eecomBlog->getCmsPageId())
        {
            $cmsPageId = $eecomBlog->getCmsPageId();
        }
        $slotConfig = $eecomBlog->getTranslation('slotConfig');


        $resolverContext = new EntityResolverContext($salesChannelContext, $request, $this->EEComBlogDefinition, $eecomBlog);

        $criteria = new Criteria([$cmsPageId]);



        $pages = $this->cmsPageLoader->load(
            $request,
            $criteria,
            $salesChannelContext,
            $slotConfig,
            $resolverContext

        );


        $page->setEecomBlog($eecomBlog);


        $page->setCmsPage($pages->first());


        $this->eventDispatcher->dispatch(
            new EEComBlogPageLoadedEvent($page, $salesChannelContext, $request)
        );

        return $page;
    }
}

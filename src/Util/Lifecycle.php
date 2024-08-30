<?php declare(strict_types=1);

namespace EECom\EEComBlog\Util;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class Lifecycle
{
    /**
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfig;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $cmsPageRepository;

    public function __construct(
        SystemConfigService       $systemConfig,
        EntityRepositoryInterface $cmsPageRepository
    )
    {
        $this->systemConfig = $systemConfig;
        $this->cmsPageRepository = $cmsPageRepository;
    }

    public function install(Context $context): void
    {
        $this->createBlogCmsListingPage($context);
    }

    private function createBlogCmsListingPage(Context $context): void
    {

        $blogDetailCmsPageId = Uuid::randomHex();

        $cmsPage = [
            [
                'id' => $blogDetailCmsPageId,
                'type' => 'blog_page',
                'name' => 'EECom Blog Detail',
                'sections' => [
                    [
                        'id' => $blogDetailCmsPageId,
                        'type' => 'default',
                        'position' => 0,
                        'sizingMode' => 'full_width'
                    ],
                ],
            ],
        ];

        $this->cmsPageRepository->create($cmsPage, $context);
        $this->systemConfig->set('eecomBlog.config.cmsBlogDetailPage', $blogDetailCmsPageId);
    }
}

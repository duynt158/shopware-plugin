<?php declare(strict_types=1);

namespace EECom\EEComBlog\Util;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\Framework\Context;


class Update
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

    public function update(UpdateContext $updateContext): void
    {
        if (version_compare($updateContext->getCurrentPluginVersion(), '1.0.4', '<')) {
            $this->updateTo104($updateContext->getContext());
        }

    }

    private function updateTo104(Context $context): void
    {
        $blogDetailCmsPageId = $this->systemConfig->get('eecomBlog.config.cmsBlogDetailPage');

        $listingCriteria = new Criteria();
        $listingCriteria->addFilter(new EqualsFilter('id', $blogDetailCmsPageId));

        $listingPageId = $this->cmsPageRepository->searchIds($listingCriteria, $context);

        if ($listingPageId->getTotal() > 0) {
            return;
        }

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

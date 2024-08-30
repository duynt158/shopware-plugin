<?php declare(strict_types=1);

namespace EECom\EEComBlog\Storefront\Page\EEComBlog;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogEntity;
use Shopware\Core\Content\Cms\CmsPageEntity;
use Shopware\Storefront\Page\Page;


class EEComBlogPage extends Page
{
    /**
     * @var EEComBlogEntity
     */
    protected $EecomBlog;

    /**
     * @var CmsPageEntity
     */
    protected $cmsPage;

    protected ?string $navigationId;


    /**
     * @return EEComBlogEntity
     */
    public function getEecomBlog(): EEComBlogEntity
    {
        return $this->EecomBlog;
    }

    /**
     * @param EEComBlogEntity $EecomBlog
     */
    public function setEecomBlog(EEComBlogEntity $EecomBlog): void
    {
        $this->EecomBlog = $EecomBlog;
    }

    public function getCmsPage(): ?CmsPageEntity
    {
        return $this->cmsPage;
    }

    public function setCmsPage(CmsPageEntity $cmsPage): void
    {
        $this->cmsPage = $cmsPage;
    }

    public function getNavigationId(): ?string
    {
        return $this->navigationId;
    }

    public function getEntityName(): string
    {
        return EEComBlogDefinition::ENTITY_NAME;
    }


}

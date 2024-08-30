<?php declare(strict_types=1);

namespace EECom\EEComBlog\Storefront\Page\EEComBlog;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\PageLoadedEvent;
use Symfony\Component\HttpFoundation\Request;

class EEComBlogPageLoadedEvent extends PageLoadedEvent
{
    /**
     * @var EEComBlogPage
     */
    protected $page;

    public function __construct(EEComBlogPage $page, SalesChannelContext $salesChannelContext, Request $request)
    {
        $this->page = $page;
        parent::__construct($salesChannelContext, $request);
    }

    public function getPage(): EEComBlogPage
    {
        return $this->page;
    }
}

<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\SalesChannel\Suggest;

use EECom\EEComBlog\Core\Content\EEComBlog\SalesChannel\Listing\EEComBlogListingResult;
use Shopware\Core\System\SalesChannel\StoreApiResponse;

class EEComBlogSuggestRouteResponse extends StoreApiResponse
{
    /**
     * @var EEComBlogListingResult
     */
    protected $object;

    public function getListingResult(): EEComBlogListingResult
    {
        return $this->object;
    }
}

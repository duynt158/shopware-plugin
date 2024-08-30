<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlogCategory\SalesChannel;

use EECom\EEComBlog\Core\Content\EEComBlogCategory\EEComBlogCategoryEntity;
use Shopware\Core\System\SalesChannel\StoreApiResponse;

class EEComBlogCategoryRouteResponse extends StoreApiResponse
{
    /**
     * @var EEComBlogCategoryEntity
     */
    protected $object;

    public function __construct(EEComBlogCategoryEntity $category)
    {
        parent::__construct($category);
    }

    public function getCategory(): EEComBlogCategoryEntity
    {
        return $this->object;
    }
}

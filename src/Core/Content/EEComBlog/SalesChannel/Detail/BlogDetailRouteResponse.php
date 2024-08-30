<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\SalesChannel\Detail;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogEntity;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\System\SalesChannel\StoreApiResponse;

class BlogDetailRouteResponse extends StoreApiResponse
{

    /**
     * @var ArrayStruct
     */
    protected $object;

    public function __construct(EEComBlogEntity $blog)
    {

        parent::__construct(new ArrayStruct([
            'blog' => $blog
        ], 'blog_detail'));
    }

    public function getResult(): ArrayStruct
    {
        return $this->object;
    }

    public function getBlog(): EEComBlogEntity
    {
        return $this->object->get('blog');
    }


}

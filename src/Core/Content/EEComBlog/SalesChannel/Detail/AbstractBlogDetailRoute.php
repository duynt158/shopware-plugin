<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\SalesChannel\Detail;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractBlogDetailRoute
{
    abstract public function getDecorated(): AbstractBlogDetailRoute;

    abstract public function load(string $blogId, Request $request, SalesChannelContext $context, Criteria $criteria): BlogDetailRouteResponse;
}

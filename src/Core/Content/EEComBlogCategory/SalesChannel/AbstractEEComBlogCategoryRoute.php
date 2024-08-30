<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlogCategory\SalesChannel;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;


abstract class AbstractEEComBlogCategoryRoute
{
    abstract public function getDecorated(): AbstractEEComBlogCategoryRoute;

    abstract public function load(string $categoryId, Request $request, SalesChannelContext $context,Criteria $criteria): EEComBlogCategoryRouteResponse;
}

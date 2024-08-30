<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\SalesChannel\Suggest;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

/**
 * This route is used for the product suggest in the page header
 */
abstract class AbstractEEComBlogSuggestRoute
{
    abstract public function getDecorated(): AbstractEEComBlogSuggestRoute;

    abstract public function load(Request $request, SalesChannelContext $context, Criteria $criteria): EEComBlogSuggestRouteResponse;
}

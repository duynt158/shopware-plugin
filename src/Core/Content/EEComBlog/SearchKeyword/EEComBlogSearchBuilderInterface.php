<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\SearchKeyword;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

interface EEComBlogSearchBuilderInterface
{
    public function build(Request $request, Criteria $criteria, SalesChannelContext $context): void;
}

<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\SearchKeyword;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Term\SearchPattern;

interface EEComBlogSearchTermInterpreterInterface
{
    public function interpret(string $word, Context $context): SearchPattern;
}

<?php declare(strict_types=1);

namespace EECom\EEComBlog\Elasticsearch\EEComBlog;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use EECom\EEComBlog\Core\Content\EEComBlog\SearchKeyword\EEComBlogSearchBuilderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Elasticsearch\Framework\ElasticsearchHelper;
use Symfony\Component\HttpFoundation\Request;

class EEComBlogSearchBuilder implements EEComBlogSearchBuilderInterface
{
    private EEComBlogSearchBuilderInterface $decorated;

    private ElasticsearchHelper $helper;

    private EEComBlogDefinition $eecomBlogDefinition;

    public function __construct(
        EEComBlogSearchBuilderInterface $decorated,
        ElasticsearchHelper             $helper,
        EEComBlogDefinition             $eecomBlogDefinition
    )
    {
        $this->decorated = $decorated;
        $this->helper = $helper;
        $this->eecomBlogDefinition = $eecomBlogDefinition;
    }

    public function build(Request $request, Criteria $criteria, SalesChannelContext $context): void
    {


        if (!$this->helper->allowSearch($this->eecomBlogDefinition, $context->getContext())) {
            $this->decorated->build($request, $criteria, $context);

            return;
        }

        $search = $request->get('search');

        if (\is_array($search)) {
            $term = implode(' ', $search);
        } else {
            $term = (string)$search;
        }

        $term = trim($term);

        if (empty($term)) {
            throw new MissingRequestParameterException('search');
        }

        // reset queries and set term to criteria.
        $criteria->resetQueries();

        // elasticsearch will interpret this on demand
        $criteria->setTerm($term);
    }
}

<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\SearchKeyword;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\AndFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Query\ScoreQuery;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Term\SearchPattern;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

class EEComBlogSearchBuilder implements EEComBlogSearchBuilderInterface
{
    /**
     * @var EEComBlogSearchTermInterpreterInterface
     */
    private $interpreter;

    public function __construct(EEComBlogSearchTermInterpreterInterface $interpreter)
    {
        $this->interpreter = $interpreter;
    }

    public function build(Request $request, Criteria $criteria, SalesChannelContext $context): void
    {


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

        $pattern = $this->interpreter->interpret($term, $context->getContext());

        foreach ($pattern->getTerms() as $searchTerm) {
            $criteria->addQuery(
                new ScoreQuery(
                    new EqualsFilter('eecom_blog.searchKeywords.keyword', $searchTerm->getTerm()),
                    $searchTerm->getScore(),
                    'eecom_blog.searchKeywords.ranking'
                )
            );
        }
        $criteria->addQuery(
            new ScoreQuery(
                new ContainsFilter('eecom_blog.searchKeywords.keyword', $pattern->getOriginal()->getTerm()),
                $pattern->getOriginal()->getScore(),
                'eecom_blog.searchKeywords.ranking'
            )
        );

        if ($pattern->getBooleanClause() !== SearchPattern::BOOLEAN_CLAUSE_AND) {
            $criteria->addFilter(new AndFilter([
                new EqualsAnyFilter('eecom_blog.searchKeywords.keyword', array_values($pattern->getAllTerms())),
                new EqualsFilter('eecom_blog.searchKeywords.languageId', $context->getContext()->getLanguageId()),
            ]));

            return;
        }

        foreach ($pattern->getTokenTerms() as $terms) {
            $criteria->addFilter(new AndFilter([
                new EqualsFilter('eecom_blog.searchKeywords.languageId', $context->getContext()->getLanguageId()),
                new EqualsAnyFilter('eecom_blog.searchKeywords.keyword', $terms),
            ]));
        }
    }
}

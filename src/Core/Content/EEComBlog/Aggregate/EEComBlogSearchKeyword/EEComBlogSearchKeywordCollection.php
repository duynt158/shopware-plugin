<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogSearchKeyword;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(EEComBlogSearchKeywordEntity $entity)
 * @method void                set(string $key, EEComBlogSearchKeywordEntity $entity)
 * @method EEComBlogSearchKeywordEntity[]    getIterator()
 * @method EEComBlogSearchKeywordEntity[]    getElements()
 * @method EEComBlogSearchKeywordEntity|null get(string $key)
 * @method EEComBlogSearchKeywordEntity|null first()
 * @method EEComBlogSearchKeywordEntity|null last()
 */
class EEComBlogSearchKeywordCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return EEComBlogSearchKeywordEntity::class;
    }
}

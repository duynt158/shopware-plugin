<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlogCategory;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(EEComBlogCategoryEntity $entity)
 * @method void                set(string $key, EEComBlogCategoryEntity $entity)
 * @method EEComBlogCategoryEntity[]    getIterator()
 * @method EEComBlogCategoryEntity[]    getElements()
 * @method EEComBlogCategoryEntity|null get(string $key)
 * @method EEComBlogCategoryEntity|null first()
 * @method EEComBlogCategoryEntity|null last()
 */
class EEComBlogCategoryCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return EEComBlogCategoryEntity::class;
    }
}

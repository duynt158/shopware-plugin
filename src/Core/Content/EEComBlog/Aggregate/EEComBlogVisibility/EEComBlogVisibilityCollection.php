<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogVisibility;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(EEComBlogVisibilityEntity $entity)
 * @method void                set(string $key, EEComBlogVisibilityEntity $entity)
 * @method EEComBlogVisibilityEntity[]    getIterator()
 * @method EEComBlogVisibilityEntity[]    getElements()
 * @method EEComBlogVisibilityEntity|null get(string $key)
 * @method EEComBlogVisibilityEntity|null first()
 * @method EEComBlogVisibilityEntity|null last()
 */
class EEComBlogVisibilityCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return EEComBlogVisibilityEntity::class;
    }
}

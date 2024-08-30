<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(EEComBlogEntity $entity)
 * @method void                set(string $key, EEComBlogEntity $entity)
 * @method EEComBlogEntity[]    getIterator()
 * @method EEComBlogEntity[]    getElements()
 * @method EEComBlogEntity|null get(string $key)
 * @method EEComBlogEntity|null first()
 * @method EEComBlogEntity|null last()
 */
class EEComBlogCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return EEComBlogEntity::class;
    }
}

<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(EEComBlogTranslationEntity $entity)
 * @method void                set(string $key, EEComBlogTranslationEntity $entity)
 * @method EEComBlogTranslationEntity[]    getIterator()
 * @method EEComBlogTranslationEntity[]    getElements()
 * @method EEComBlogTranslationEntity|null get(string $key)
 * @method EEComBlogTranslationEntity|null first()
 * @method EEComBlogTranslationEntity|null last()
 */
class EEComBlogTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return EEComBlogTranslationEntity::class;
    }
}

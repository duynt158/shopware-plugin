<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlogCategory\Aggregate\EEComBlogCategoryTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(EEComBlogCategoryTranslationEntity $entity)
 * @method void                set(string $key, EEComBlogCategoryTranslationEntity $entity)
 * @method EEComBlogCategoryTranslationEntity[]    getIterator()
 * @method EEComBlogCategoryTranslationEntity[]    getElements()
 * @method EEComBlogCategoryTranslationEntity|null get(string $key)
 * @method EEComBlogCategoryTranslationEntity|null first()
 * @method EEComBlogCategoryTranslationEntity|null last()
 */
class EEComBlogCategoryTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return EEComBlogCategoryTranslationEntity::class;
    }
}

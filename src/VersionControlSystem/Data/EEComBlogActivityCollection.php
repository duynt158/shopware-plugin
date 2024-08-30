<?php declare(strict_types=1);

namespace EECom\EEComBlog\VersionControlSystem\Data;

use EECom\EEComBlog\Common\EntityCollectionAddon;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\Struct\ArrayEntity;

/**
 * @method void                add(ArrayEntity $entity)
 * @method void                set(string $key, ArrayEntity $entity)
 * @method ArrayEntity[]    getIterator()
 * @method ArrayEntity[]    getElements()
 * @method ArrayEntity|null get(string $key)
 * @method ArrayEntity|null first()
 * @method ArrayEntity|null last()
 */
class EEComBlogActivityCollection extends EntityCollection
{
    use EntityCollectionAddon;

    public function getApiAlias(): string
    {
        return 'eecom_blog_activity_collection';
    }
    protected function getExpectedClass(): string
    {
        return ArrayEntity::class;
    }
}
